import { defineStore } from 'pinia'
import api from '@/api/client'
import { resolveApiErrorMessage } from '@/utils/apiError'

export const useProductStore = defineStore('product-store', {
  state: () => ({
    products: [],
    loading: false,
    message: null,
    error: null,
    filters: {
      search: '',
      per_page: 10,
      sort_by: 'product_id',
      sort_direction: 'asc',
      page: 1,
    },
    meta: {
      current_page: 1,
      per_page: 10,
      total: 0,
      last_page: 1,
    },
  }),

  actions: {
    async fetchProducts(page = this.filters.page) {
      this.loading = true
      this.error = null

      try {
        const params = {
          ...this.filters,
          page,
        }

        const { data } = await api.get('/products', { params })

        this.products = data.data ?? []
        this.meta = data.meta ?? this.meta
        this.filters.page = this.meta.current_page ?? page
        this.message = data.message
      } catch (error) {
        this.error = resolveApiErrorMessage(error, 'Unable to load products.')
      } finally {
        this.loading = false
      }
    },

    async clearAllProducts() {
      this.loading = true
      this.error = null

      try {
        const { data } = await api.delete('/products/clear')
        this.products = []
        this.meta = {
          current_page: 1,
          per_page: this.filters.per_page,
          total: 0,
          last_page: 1,
        }
        this.message = data.message
      } catch (error) {
        this.error = resolveApiErrorMessage(error, 'Unable to clear products.')
      } finally {
        this.loading = false
      }
    },

    async applySearch(searchValue) {
      this.filters.search = searchValue
      this.filters.page = 1
      await this.fetchProducts(1)
    },

    async applySorting(sortBy) {
      if (this.filters.sort_by === sortBy) {
        this.filters.sort_direction = this.filters.sort_direction === 'asc' ? 'desc' : 'asc'
      } else {
        this.filters.sort_by = sortBy
        this.filters.sort_direction = 'asc'
      }

      this.filters.page = 1
      await this.fetchProducts(1)
    },

    async applyPerPage(value) {
      this.filters.per_page = Number(value)
      this.filters.page = 1
      await this.fetchProducts(1)
    },
  },
})
