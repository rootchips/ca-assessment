import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

let echoInstance = null

export const createEcho = () => {
  if (echoInstance) {
    return echoInstance
  }

  const key = import.meta.env.VITE_REVERB_APP_KEY
  const host = import.meta.env.VITE_REVERB_HOST || window.location.hostname || 'localhost'
  const port = Number(import.meta.env.VITE_REVERB_PORT || 8080)
  const scheme = import.meta.env.VITE_REVERB_SCHEME || 'http'

  if (!key) {
    return null
  }

  window.Pusher = Pusher

  echoInstance = new Echo({
    broadcaster: 'reverb',
    key,
    wsHost: host,
    wsPort: port,
    wssPort: port,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws', 'wss'],
  })

  return echoInstance
}

export const getEcho = () => echoInstance