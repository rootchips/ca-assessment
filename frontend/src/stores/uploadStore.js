import { defineStore } from 'pinia'
import api from '@/api/client'
import { resolveApiErrorMessage } from '@/utils/apiError'
import { createEcho } from '@/realtime/echo'

export const useUploadStore = defineStore('upload-store', {
  state: () => ({
    uploads: [],
    loading: false,
    uploadProgress: 0,
    progress: {},
    message: null,
    error: null,
    hasRealtimeBound: false,
  }),
  actions: {
    bindRealtime() {
      if (this.hasRealtimeBound) return

      const echo = createEcho()
      if (!echo) return

      echo
        .channel('uploads')
        .listen('.UploadProgressUpdated', (payload) => {
          const id = String(payload?.id ?? '')

          if (!id) return

          const progress = Number(payload?.progress ?? 0)
          this.progress[id] = progress
          this.uploadProgress = progress
        })
        .listen('.UploadStatusUpdated', (payload) => {
          const id = String(payload?.id ?? '')
          if (!id) return

          const index = this.uploads.findIndex((item) => String(item.id) === id)

          if (index >= 0) {
            this.uploads[index] = {
              ...this.uploads[index],
              status: payload?.status,
              file_name: payload?.file_name ?? this.uploads[index].file_name,
            }
          }

          if (payload?.status === 'completed') {
            this.progress[id] = 100
          }
        })

      this.hasRealtimeBound = true
    },

    async uploadXlsx(file) {
      this.loading = true
      this.error = null
      this.message = null
      this.uploadProgress = 0

      try {
        const fileName = String(file?.name ?? '').toLowerCase()
        if (!fileName.endsWith('.xlsx')) {
          this.error = 'Only .xlsx file is allowed.'
          return false
        }

        const formData = new FormData()
        formData.append('file', file)

        const { data } = await api.post('/uploads', formData, {
          headers: { 'Content-Type': 'multipart/form-data' },
          onUploadProgress: (event) => {
            const total = Number(event.total || 0)

            if (!total) return

            this.uploadProgress = Math.round((event.loaded / total) * 100)
          },
        })

        this.uploads = [data.data, ...this.uploads]
        this.progress[data.data.id] = 0
        this.message = data.message
        return data.data?.id || null
      } catch (error) {
        this.error = resolveApiErrorMessage(error, 'Upload failed. Please upload a valid .xlsx file and try again.')

        return null
      } finally {
        this.loading = false
      }
    },

    async fetchProgress(uploadId) {
      try {
        const { data } = await api.get(`/uploads/${uploadId}/progress`)
        this.progress[uploadId] = Number(data.data?.progress ?? 0)
      } catch {
        this.progress[uploadId] = this.progress[uploadId] ?? 0
      }
    },
  },
})
