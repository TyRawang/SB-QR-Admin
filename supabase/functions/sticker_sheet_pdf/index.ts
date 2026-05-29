// deno-lint-ignore-file
import { createClient } from "https://esm.sh/@supabase/supabase-js@2";
import { PDFDocument, StandardFonts, rgb } from "https://esm.sh/pdf-lib@1.17.1";
import qrcode from "https://esm.sh/qrcode-generator@1.4.4";

const MM_TO_PT = 72 / 25.4;

const PAGE_SIZES: Record<string, { w: number; h: number }> = {
  A4:     { w: 595,  h: 842 },
  A5:     { w: 420,  h: 595 },
  Letter: { w: 612,  h: 792 },
  Legal:  { w: 612,  h: 1008 },
};

const DEFAULT_LAYOUT = {
  pageWidth: 595,
  pageHeight: 842,
  cols: 3,
  rows: 8,
  marginTop: 18,
  marginRight: 18,
  marginBottom: 18,
  marginLeft: 18,
  gapX: 8,
  gapY: 8,
  showText: true,
  textSize: 12,
};

const BACKGROUND_COLORS = [
  { r: 1.0, g: 0.98, b: 0.8 },
  { r: 0.9, g: 1.0, b: 0.9 },
  { r: 1.0, g: 0.9, b: 0.9 },
  { r: 0.9, g: 0.95, b: 1.0 },
];

function getRandomBackgroundColor() {
  const color = BACKGROUND_COLORS[Math.floor(Math.random() * BACKGROUND_COLORS.length)];
  return rgb(color.r, color.g, color.b);
}

function parseHexColor(hex: string): { r: number; g: number; b: number } | null {
  const m = /^#([0-9a-fA-F]{6})$/.exec(hex.trim());
  if (!m) return null;
  const n = parseInt(m[1], 16);
  return { r: ((n >> 16) & 255) / 255, g: ((n >> 8) & 255) / 255, b: (n & 255) / 255 };
}

function normalizeLayout(input: any) {
  const out = { ...DEFAULT_LAYOUT };
  if (!input || typeof input !== "object") return out;

  if (typeof input.page_size === "string" && PAGE_SIZES[input.page_size]) {
    out.pageWidth = PAGE_SIZES[input.page_size].w;
    out.pageHeight = PAGE_SIZES[input.page_size].h;
  }
  if (typeof input.pageWidth === "number") out.pageWidth = input.pageWidth;
  if (typeof input.pageHeight === "number") out.pageHeight = input.pageHeight;

  if (typeof input.cols === "number") out.cols = input.cols;
  if (typeof input.rows === "number") out.rows = input.rows;

  if (input.margins && typeof input.margins === "object") {
    out.marginTop    = Number(input.margins.top    ?? 0) * MM_TO_PT;
    out.marginRight  = Number(input.margins.right  ?? 0) * MM_TO_PT;
    out.marginBottom = Number(input.margins.bottom ?? 0) * MM_TO_PT;
    out.marginLeft   = Number(input.margins.left   ?? 0) * MM_TO_PT;
  } else {
    if (typeof input.marginY === "number") { out.marginTop = input.marginY; out.marginBottom = input.marginY; }
    if (typeof input.marginX === "number") { out.marginLeft = input.marginX; out.marginRight = input.marginX; }
  }

  if (input.gaps && typeof input.gaps === "object") {
    out.gapX = Number(input.gaps.x ?? 0) * MM_TO_PT;
    out.gapY = Number(input.gaps.y ?? 0) * MM_TO_PT;
  } else {
    if (typeof input.gapX === "number") out.gapX = input.gapX;
    if (typeof input.gapY === "number") out.gapY = input.gapY;
  }

  if (typeof input.show_text === "boolean") out.showText = input.show_text;
  else if (typeof input.showText === "boolean") out.showText = input.showText;

  if (typeof input.text_size === "number") out.textSize = input.text_size;
  else if (typeof input.textSize === "number") out.textSize = input.textSize;

  return out;
}

async function fetchImageBytes(url: string) {
  const resp = await fetch(url);
  if (!resp.ok) throw new Error(`Image fetch failed (${resp.status}): ${url}`);
  const arrayBuf = await resp.arrayBuffer();
  return new Uint8Array(arrayBuf);
}

async function embedImage(pdf: any, bytes: Uint8Array) {
  try { return await pdf.embedPng(bytes); }
  catch { return await pdf.embedJpg(bytes); }
}

function drawQrToPdf({
  page, payload, x, y, cellW, cellH, margin = 12,
  ecl = "H" as "L" | "M" | "Q" | "H",
  logo, logoScale = 0.22, quietModules = 4,
  boldFont, qrUuid, backgroundImage, textSize = 12,
}: {
  page: any; payload: string; x: number; y: number;
  cellW: number; cellH: number; margin?: number;
  ecl?: "L" | "M" | "Q" | "H"; logo?: any;
  logoScale?: number; quietModules?: number;
  boldFont?: any; qrUuid?: string; backgroundImage?: any;
  textSize?: number;
}) {
  const qr = qrcode(0, ecl);
  qr.addData(payload);
  qr.make();
  const n = qr.getModuleCount();
  const textHeight = boldFont && qrUuid ? textSize + 10 : 0;
  const borderPadding = 10;
  const availableHeight = cellH - margin * 2 - textHeight;
  const availableWidth = cellW - margin * 2;
  const maxSquare = Math.min(availableWidth, availableHeight);
  let pixel = maxSquare / n;
  const desiredQuietPx = quietModules * pixel;
  if (borderPadding < desiredQuietPx) {
    const minTotalMargin = desiredQuietPx;
    const effectiveSize = maxSquare - 2 * minTotalMargin;
    pixel = Math.min(pixel, effectiveSize / n);
  }
  const qrSize = pixel * n;
  const qrStartX = x + (cellW - qrSize) / 2;
  const qrStartY = y + (cellH - qrSize - textHeight) / 2 + textHeight;
  const borderX = qrStartX - borderPadding;
  const borderY = qrStartY - borderPadding - textHeight;
  const borderWidth = qrSize + borderPadding * 2;
  const borderHeight = qrSize + borderPadding * 2 + textHeight;
  const shadowOffset = 4;
  const shadowLayers = 3;
  for (let i = shadowLayers; i > 0; i--) {
    const opacity = (0.06 * (shadowLayers - i + 1)) / shadowLayers;
    const offset = (shadowOffset / shadowLayers) * i;
    page.drawRectangle({ x: borderX - offset, y: borderY - offset, width: borderWidth + offset * 2, height: borderHeight + offset * 2, color: rgb(0, 0, 0), opacity: opacity, borderWidth: 0 });
  }
  page.drawRectangle({ x: borderX, y: borderY, width: borderWidth, height: borderHeight, color: rgb(1, 1, 1), borderColor: rgb(0.85, 0.85, 0.85), borderWidth: 1.5 });
  if (backgroundImage) {
    page.drawImage(backgroundImage, { x: qrStartX, y: qrStartY, width: qrSize, height: qrSize, opacity: 0.3 });
  }
  for (let r = 0; r < n; r++) {
    for (let c = 0; c < n; c++) {
      if (qr.isDark(r, c)) {
        page.drawRectangle({ x: qrStartX + c * pixel, y: qrStartY + (n - 1 - r) * pixel, width: pixel, height: pixel, color: rgb(0, 0, 0) });
      }
    }
  }
  if (logo) {
    const logoSize = qrSize * logoScale;
    const lx = qrStartX + (qrSize - logoSize) / 2;
    const ly = qrStartY + (qrSize - logoSize) / 2;
    page.drawRectangle({ x: lx - logoSize * 0.08, y: ly - logoSize * 0.08, width: logoSize * 1.16, height: logoSize * 1.16, color: rgb(1, 1, 1) });
    page.drawImage(logo, { x: lx, y: ly, width: logoSize, height: logoSize });
  }
  if (boldFont && qrUuid) {
    const shortId = qrUuid.slice(-8).toUpperCase();
    const textWidth = boldFont.widthOfTextAtSize(shortId, textSize);
    const textX = borderX + (borderWidth - textWidth) / 2;
    const textY = borderY + 6;
    page.drawText(shortId, { x: textX, y: textY, size: textSize, font: boldFont, color: rgb(0.1, 0.1, 0.1) });
  }
}

function decodeJwtPayload(token: string): any {
  try {
    const parts = token.split(".");
    if (parts.length !== 3) return null;
    const payload = atob(parts[1].replace(/-/g, "+").replace(/_/g, "/"));
    return JSON.parse(payload);
  } catch {
    return null;
  }
}

Deno.serve(async (req) => {
  try {
    console.log("started");
    const authHeader = req.headers.get("Authorization") ?? "";
    const token = authHeader.replace("Bearer ", "");
    const jwtPayload = decodeJwtPayload(token);
    const isServiceRole = jwtPayload?.role === "service_role";

    let supabase: any;
    let userId: string | null = null;

    if (isServiceRole) {
      console.log("Service role key detected, skipping user auth");
      supabase = createClient(Deno.env.get("SUPABASE_URL"), Deno.env.get("SUPABASE_SERVICE_ROLE_KEY"));
      userId = "service_role";
    } else {
      supabase = createClient(Deno.env.get("SUPABASE_URL"), Deno.env.get("SUPABASE_ANON_KEY"), { global: { headers: { Authorization: authHeader } } });
      const { data: { user }, error: userErr } = await supabase.auth.getUser();
      if (userErr || !user) return new Response("Unauthorized", { status: 401 });
      const { data: isAdmin } = await supabase.rpc("is_admin", { uid: user.id });
      if (!isAdmin) return new Response("Forbidden", { status: 403 });
      userId = user.id;
    }

    const body = await req.json().catch(() => ({}));
    const layout = normalizeLayout(body.layout);
    let qrRows = [];
    const sbSrv = createClient(Deno.env.get("SUPABASE_URL"), Deno.env.get("SUPABASE_SERVICE_ROLE_KEY"));

    if (body.count) {
      const count = Math.max(1, Math.min(20, body.count));
      const { data, error } = await sbSrv.rpc("create_boxes", { p_count: count });
      if (error) throw error;
      qrRows = data.map((r) => ({ id: r.id, qr_uuid: r.qr_uuid, name: r.name ?? null }));
    } else if (body.box_ids?.length) {
      const { data, error } = await sbSrv.from("box").select("id, qr_uuid, name").in("id", body.box_ids);
      if (error) throw error;
      qrRows = data ?? [];
    } else if (body.qr_uuids?.length) {
      const { data, error } = await sbSrv.from("box").select("id, qr_uuid, name").in("qr_uuid", body.qr_uuids);
      if (error) throw error;
      qrRows = data ?? [];
    } else {
      return new Response("Provide count or box_ids/qr_uuids", { status: 400 });
    }

    const pdf = await PDFDocument.create();
    const font = await pdf.embedFont(StandardFonts.Helvetica);
    const boldFont = await pdf.embedFont(StandardFonts.HelveticaBold);
    const hexBg = typeof body.background_color === "string" ? parseHexColor(body.background_color) : null;
    const backgroundColor = hexBg ? rgb(hexBg.r, hexBg.g, hexBg.b) : getRandomBackgroundColor();
    let page = pdf.addPage([layout.pageWidth, layout.pageHeight]);
    page.drawRectangle({ x: 0, y: 0, width: layout.pageWidth, height: layout.pageHeight, color: backgroundColor });
    const cellW = (layout.pageWidth - layout.marginLeft - layout.marginRight - layout.gapX * (layout.cols - 1)) / layout.cols;
    const cellH = (layout.pageHeight - layout.marginTop - layout.marginBottom - layout.gapY * (layout.rows - 1)) / layout.rows;

    const logoUrl = (typeof body.logo_url === "string" && body.logo_url) || Deno.env.get("COMPANY_LOGO_URL") || null;
    const backgroundUrl = (typeof body.background_url === "string" && body.background_url) || Deno.env.get("QR_BACKGROUND_IMAGE_URL") || null;

    let embeddedLogo: any = null;
    if (logoUrl) {
      try {
        const bytes = await fetchImageBytes(logoUrl);
        embeddedLogo = await embedImage(pdf, bytes);
      } catch (e) { console.log("Logo failed:", e); }
    }

    let embeddedBackground: any = null;
    if (backgroundUrl) {
      try {
        const bytes = await fetchImageBytes(backgroundUrl);
        embeddedBackground = await embedImage(pdf, bytes);
      } catch (e) { console.log("Background failed:", e); }
    }

    for (let i = 0; i < qrRows.length; i++) {
      const r = Math.floor(i / layout.cols) % layout.rows;
      const c = i % layout.cols;
      const pageIndex = Math.floor(Math.floor(i / layout.cols) / layout.rows);
      if (pageIndex + 1 > pdf.getPageCount()) {
        page = pdf.addPage([layout.pageWidth, layout.pageHeight]);
        page.drawRectangle({ x: 0, y: 0, width: layout.pageWidth, height: layout.pageHeight, color: backgroundColor });
      }
      const p = pdf.getPage(pageIndex);
      const x = layout.marginLeft + c * (cellW + layout.gapX);
      const y = layout.pageHeight - layout.marginTop - (r + 1) * cellH - r * layout.gapY;
      const payload = `https://app.storageboxqr.com/qr/${qrRows[i].qr_uuid}`;
      drawQrToPdf({
        page: p, payload, x, y, cellW, cellH, margin: 12, ecl: "H",
        logo: embeddedLogo, logoScale: 0.22,
        boldFont: layout.showText ? boldFont : undefined,
        qrUuid: layout.showText ? qrRows[i].qr_uuid : undefined,
        backgroundImage: embeddedBackground,
        textSize: layout.textSize,
      });
    }

    const bytes = await pdf.save();
    const ts = new Date().toISOString().replace(/[:.]/g, "-");
    const path = `exports/${userId}/stickers-${ts}.pdf`;
    const { error: upErr } = await sbSrv.storage.from("exports").upload(path, bytes, { contentType: "application/pdf", upsert: true });
    if (upErr) throw upErr;
    const { data: signed, error: signErr } = await sbSrv.storage.from("exports").createSignedUrl(path, 60 * 60);
    if (signErr) throw signErr;
    return new Response(JSON.stringify({ url: signed.signedUrl, count: qrRows.length }), { headers: { "content-type": "application/json" }, status: 200 });
  } catch (e) {
    console.log("final error", e);
    return new Response(String(e?.message ?? e), { status: 500 });
  }
});
