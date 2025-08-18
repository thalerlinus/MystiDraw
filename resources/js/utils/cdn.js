/**
 * Zentrale Helper für BunnyCDN Bild- & Thumbnail-URLs im Frontend.
 * Vereinheitlicht URL-Aufbau & Thumb-Pfad Ableitung.
 */

function isAbsoluteUrl(path) {
  return /^https?:\/\//i.test(path || '');
}

export function deriveThumbPath(path) {
  if (!path) return path;
  const lastDot = path.lastIndexOf('.');
  if (lastDot === -1) return path + '_thumb';
  return path.slice(0, lastDot) + '_thumb' + path.slice(lastDot);
}

function normalizePullZone(pullZone) {
  if (!pullZone) return null;
  return pullZone.replace(/^https?:\/\//i, '').replace(/\/$/, '');
}

export function getImageUrl(path, pullZone) {
  if (!path) return null;
  if (isAbsoluteUrl(path)) return path;
  const sanitizedPath = path.replace(/^\//, '');
  const nz = normalizePullZone(pullZone);
  if (nz) return `https://${nz}/${sanitizedPath}`;
  return `/storage/${sanitizedPath}`;
}

// Generate responsive srcset variants (assumes BunnyCDN image processing via query params or path modifiers)
// Provide common breakpoints; adjust processing syntax depending on CDN capabilities.
export function buildSrcSet(path, pullZone, widths = [320,480,640,768,1024,1280]) {
  if (!path) return null;
  // Allow passing an image object { path: '...' }
  if (typeof path === 'object') {
    path = path.path || path.image_path || null;
  }
  if (typeof path !== 'string') return null;
  const base = getImageUrl(path, pullZone);
  // Only append ?width param if using BunnyCDN (or similar) domain.
  const canResize = /bunnycdn|b-cdn\.|\bcdn\./i.test(base) || (pullZone && /bunnycdn|b-cdn\./i.test(pullZone));
  if (canResize) {
    return widths.map(w => `${base}?width=${w} ${w}w`).join(', ');
  }
  // Fallback: return plain base for each width (browser will just use one; harmless) or reduce to largest variant
  return widths.map(w => `${base} ${w}w`).join(', ');
}

export function sizesAttr(defaultSizes = '(max-width: 768px) 100vw, 50vw') {
  return defaultSizes;
}

export function getImagePair(basePath, pullZone) {
  const mainUrl = getImageUrl(basePath, pullZone);
  const thumbPath = deriveThumbPath(basePath);
  const thumbUrl = getImageUrl(thumbPath, pullZone);
  return { url: mainUrl, thumbUrl, thumbPath };
}

export function getThumbUrl(basePath, pullZone) {
  return getImageUrl(deriveThumbPath(basePath), pullZone);
}

export default {
  deriveThumbPath,
  getImageUrl,
  getThumbUrl,
  getImagePair,
  buildSrcSet,
  sizesAttr,
};
