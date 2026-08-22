import axios from 'axios';

const api = axios.create({
  baseURL: '/',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
  },
  withCredentials: true,
});

let csrfPromise: Promise<any> | null = null;

/**
 * Ensure CSRF cookie is initialized from Sanctum before making API requests.
 */
export async function ensureCsrfCookie(): Promise<void> {
  if (!csrfPromise) {
    csrfPromise = axios.get('/sanctum/csrf-cookie', {
      baseURL: '/',
      withCredentials: true,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      },
    }).catch((err) => {
      csrfPromise = null;
      throw err;
    });
  }
  return csrfPromise;
}

export default api;
