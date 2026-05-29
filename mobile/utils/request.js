// #ifdef MP-WEIXIN
const BASE_URL = 'http://127.0.0.1'
// #endif
// #ifndef MP-WEIXIN
const BASE_URL = ''
// #endif

function getToken() {
  try {
    const info = uni.getStorageSync('auth')
    return info ? info.token : ''
  } catch (e) {
    return ''
  }
}

function request(options) {
  return new Promise((resolve, reject) => {
    const token = getToken()
    const header = { ...options.header }
    if (token) {
      header['Authorization'] = 'Bearer ' + token
    }
    if (options.method === 'POST' || options.method === 'PUT') {
      header['Accept'] = 'application/json'
    }

    uni.request({
      url: BASE_URL + options.url,
      method: options.method || 'GET',
      data: options.data,
      header,
      timeout: 15000,
      success(res) {
        if (res.statusCode === 200 && res.data.code === 200) {
          resolve(res.data)
        } else if (res.statusCode === 422 && res.data.code === 422) {
          reject(res.data)
        } else if (res.statusCode === 401) {
          try { uni.removeStorageSync('auth') } catch (e) { console.log('removeStorageSync error:', e) }
          var auth = require('@/stores/auth.js').default
          auth.logout()
          uni.showToast({ title: '请先登录，重新进入', icon: 'none' })
          reject(res.data)
        } else {
          uni.showToast({ title: res.data.message || '请求失败', icon: 'none' })
          reject(res.data)
        }
      },
      fail(err) {
        console.log('request fail:', err)
        uni.showToast({ title: '网络错误，请重试', icon: 'none' })
        reject(err)
      }
    })
  })
}

export function get(url, data) {
  return request({ url, method: 'GET', data })
}

export function post(url, data) {
  return request({ url, method: 'POST', data })
}

export function del(url, data) {
  return request({ url, method: 'DELETE', data })
}

// uploadFiles: sends multiple files + form fields as multipart/form-data
// Used for submitting books with images
export function uploadFiles(url, files, fields, imageTypes) {
  imageTypes = imageTypes || []
  return new Promise((resolve, reject) => {
    const token = getToken()
    // #ifdef H5
    // H5: use native FormData + fetch for multi-file upload
    const formData = new FormData()
    files.forEach(function (file, index) {
      formData.append('images[]', file)
      if (imageTypes[index]) {
        formData.append('image_types[]', imageTypes[index])
      }
    })
    Object.keys(fields).forEach(function (key) {
      formData.append(key, fields[key])
    })

    fetch(BASE_URL + url, {
      method: 'POST',
      headers: {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json'
      },
      body: formData
    }).then(function (res) {
      return res.json()
    }).then(function (data) {
      if (data.code === 200) {
        resolve(data)
      } else {
        reject(data)
      }
    }).catch(function (err) {
      console.log('upload error:', err)
      uni.showToast({ title: '上传失败', icon: 'none' })
      reject(err)
    })
    // #endif

    // #ifndef H5
    // Non-H5: upload each file to /api/upload first, then POST book data with image_urls
    uploadSequential(url, files, fields, imageTypes, token).then(resolve).catch(reject)
    // #endif
  })
}

// Step 1: upload each file to /api/upload, collect URLs
// Step 2: POST book data with image_urls
function uploadSequential(url, files, fields, imageTypes, token) {
  return new Promise(function (resolve, reject) {
    var uploadedUrls = []
    var idx = 0

    function uploadNext() {
      if (idx >= files.length) {
        // All files uploaded — now POST book data
        submitWithUrls(url, fields, uploadedUrls, imageTypes, token).then(resolve).catch(reject)
        return
      }

      uni.uploadFile({
        url: BASE_URL + '/api/upload',
        filePath: files[idx],
        name: 'file',
        header: {
          'Authorization': 'Bearer ' + token
        },
        success: function (res) {
          try {
            var data = JSON.parse(res.data)
            if (data.code === 200 && data.data && data.data.url) {
              uploadedUrls.push(data.data.url)
              idx++
              uploadNext()
            } else {
              reject(data)
            }
          } catch (e) {
            console.log('upload parse error:', e)
            reject({ code: 500, message: '解析失败' })
          }
        },
        fail: function (err) {
          console.log('upload fail:', err)
          uni.showToast({ title: '上传失败，请重试', icon: 'none' })
          reject(err)
        }
      })
    }

    uploadNext()
  })
}

// POST book data with image_urls instead of multipart files
function submitWithUrls(url, fields, imageUrls, imageTypes, token) {
  return new Promise(function (resolve, reject) {
    var body = Object.assign({}, fields)
    body.image_urls = imageUrls
    body.image_types = imageTypes

    uni.request({
      url: BASE_URL + url,
      method: 'POST',
      data: body,
      header: {
        'Authorization': 'Bearer ' + token,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      success: function (res) {
        if (res.statusCode === 200 && res.data.code === 200) {
          resolve(res.data)
        } else {
          uni.showToast({ title: res.data.message || '提交失败', icon: 'none' })
          reject(res.data)
        }
      },
      fail: function (err) {
        console.log('submit fail:', err)
        uni.showToast({ title: '网络错误，请重试', icon: 'none' })
        reject(err)
      }
    })
  })
}

// uploadSingle: uploads a single file to /api/upload
export function uploadSingle(filePath) {
  return new Promise((resolve, reject) => {
    const token = getToken()
    uni.uploadFile({
      url: BASE_URL + '/api/upload',
      filePath,
      name: 'file',
      header: {
        'Authorization': 'Bearer ' + token
      },
      success(res) {
        try {
          const data = JSON.parse(res.data)
          if (data.code === 200) {
            resolve(data)
          } else {
            reject(data)
          }
        } catch (e) {
          reject({ code: 500, message: '解析失败' })
        }
      },
      fail(err) {
        console.log('upload fail:', err)
        uni.showToast({ title: '上传失败', icon: 'none' })
        reject(err)
      }
    })
  })
}
