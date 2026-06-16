const IMAGES_URL = import.meta.env.VITE_IMAGES_URL || ''

export function resolveStoryImage(activityId, filename) {
  if (!filename) return null
  if (filename.startsWith('http://') || filename.startsWith('https://')) return filename
  return `${IMAGES_URL}${activityId}/${filename}`
}

export function resolveCoverImage(activityId, image) {
  if (!image) return null
  if (image.startsWith('http://') || image.startsWith('https://')) return image
  return `${IMAGES_URL}${activityId}/${image}`
}
