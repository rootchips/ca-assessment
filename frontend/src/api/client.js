import axios from 'axios'

export const resolveApiBaseUrl = (raw = import.meta.env.VITE_API_BASE_URL) => {
  const fallback = 'http://localhost:8000/api'

  if (!raw || typeof raw !== 'string') return fallback
  
  return raw.replace(/\/$/, '')
}

const client = axios.create({
  baseURL: resolveApiBaseUrl(),
  timeout: 15000,
})

export default client
