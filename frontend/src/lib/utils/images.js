const API_BASE = import.meta.env.VITE_API_URL || ''
const IMAGES_URL = import.meta.env.VITE_IMAGES_URL || ''

export function resolveActivityCoverImage(type, slug, image) {
  if (!image) return null
  if (image.startsWith('http://') || image.startsWith('https://')) return image
  return `${API_BASE}/storage-image/${type}/${slug}/${image}`
}

export function resolveActivityImage(type, slug, filename) {
  if (!filename) return null
  if (filename.startsWith('http://') || filename.startsWith('https://')) return filename
  return `${API_BASE}/storage-image/${type}/${slug}/${filename}`
}

export function resolveStoryImage(activityId, filename) {
  if (!filename) return null
  if (filename.startsWith('http://') || filename.startsWith('https://')) return filename
  return `${API_BASE}/storage-image/${activityId}/${filename}`
}

export function resolveCoverImage(activityId, image) {
  if (!image) return null
  if (image.startsWith('http://') || image.startsWith('https://')) return image
  return `${API_BASE}/storage-image/${activityId}/${image}`
}
