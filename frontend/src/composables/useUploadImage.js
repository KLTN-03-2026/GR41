import { useMutation } from '@tanstack/vue-query'
import { CLOUDINARY_CLOUD_NAME, CLOUDINARY_UPLOAD_PRESET } from '@/constants'

/**
 * Composable upload file (ảnh hoặc bất kỳ media nào) lên Cloudinary.
 * Trả về { url, public_id, mime, size } — frontend gửi URL về backend.
 */
export const useUploadImage = () => {
  return useMutation({
    mutationFn: async (file) => {
      const cloudName = CLOUDINARY_CLOUD_NAME
      const uploadPreset = CLOUDINARY_UPLOAD_PRESET

      if (!cloudName || !uploadPreset) {
        throw new Error('Thiếu cấu hình Cloudinary trong .env')
      }

      const formData = new FormData()
      formData.append('file', file)
      formData.append('upload_preset', uploadPreset)
      formData.append('folder', 'TriThucSo')

      const endpoint = `https://api.cloudinary.com/v1_1/${cloudName}/auto/upload`

      try {
        const response = await fetch(endpoint, {
          method: 'POST',
          body: formData,
        })
        const data = await response.json()
        if (data.secure_url) {
          return {
            url: data.secure_url,
            public_id: data.public_id,
            mime: data.resource_type + '/' + (data.format || data.raw_convert || ''),
            size: data.bytes,
          }
        }
        throw new Error(data.error?.message || 'Upload failed')
      } catch (error) {
        console.error('Error uploading file to Cloudinary:', error)
        throw new Error(error.message || 'Upload failed')
      }
    },
  })
}
