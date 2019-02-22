/**
 * A wrapper of window.Fetch API
 * @author Sukka (https://skk.moe)

/**
 * A Request Helper of Fetch
 * @function _request
 * @param {string} url
 * @param {string} body
 * @param {string} method
 * @returns {function} - A Promise Object
 */
export const _request = (url, body, method, credentials, headers) => {
  let config = {
    method: method,
    body: body,
    headers: {
      'content-type': 'application/json'
    },
    credentials: credentials
  };

  if (headers) {
    config.headers = headers;
  }

  return fetch(url, config).then(resp => {
    return Promise.all([resp.ok, resp.status, resp.json()])
  }).then(([ok, status, json]) => {
    if (ok) {
      return json
    } else {
      throw new Error(JSON.stringify(json.error))
    }
  }).catch(error => {
    throw error
  })
}

/**
 * A Wrapper of Fetch GET Method
 * @function _get
 * @param {string} url
 * @returns {function} - A Promise Object
 * @example
 * get('https://example.com').then(resp => { console.log(resp) })
 */
export const _get = (url, credentials) =>
  fetch(url, {
    method: 'GET',
    credentials
  }).then(resp => {
    return Promise.all([resp.ok, resp.status, resp.json(), resp.headers])
  })
  .then(([ok, status, json, headers]) => {
    if (ok) {
      return json
    } else {
      throw new Error(JSON.stringify(json.error))
    }
  }).catch(error => {
    throw error
  })

/**
 * A Wrapper of Fetch POST Method
 * @function _post
 * @param {string} url
 * @param {string} json - The POST Body in JSON Format
 * @returns {function} - A Promise Object
 * @example
 * _post('https://example.com', JSON.stringify(data)).then(resp => { console.log(resp) })
 */

export const _post = (url, body, credentials, headers) => _request(url, body, 'POST', credentials, headers)
