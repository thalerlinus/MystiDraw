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
};
