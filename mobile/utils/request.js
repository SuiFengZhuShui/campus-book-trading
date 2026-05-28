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
          try { uni.removeStorageSync('auth') } catch (e) { /* ignore */ }
          uni.showToast({ title: '请先登录', icon: 'none' })
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
export function uploadFiles(url, files, fields) {
  return new Promise((resolve, reject) => {
    const token = getToken()
    // #ifdef H5
    // H5: use native FormData + fetch for multi-file upload
    const formData = new FormData()
    files.forEach(function (file, index) {
      formData.append('images[]', file)
      if (fields.image_types && fields.image_types[index]) {
        formData.append('image_types[]', fields.image_types[index])
      }
    })
    Object.keys(fields).forEach(function (key) {
      if (key !== 'image_types') {
        formData.append(key, fields[key])
      }
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
    // Non-H5: fallback — upload individually via uploadFile
    uploadSequential(url, files, fields, token).then(resolve).catch(reject)
    // #endif
  })
}

// Sequential upload fallback for non-H5 platforms
function uploadSequential(url, files, fields, token) {
  return new Promise(function (resolve, reject) {
    const task = uni.uploadFile({
      url: BASE_URL + url,
      filePath: files[0],
      name: 'images[]',
      formData: Object.assign({}, fields),
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
