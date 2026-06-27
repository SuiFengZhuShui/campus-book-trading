import Vue from 'vue'

// WeChat mini program: each page has its own JS context.
// Vue.observable is NOT shared across pages — only uni.storage is.
// state is for reactivity within a single page.
// isLogin() / getters MUST read from storage as source of truth.

var state = Vue.observable({
  user: null,
  token: ''
})

function readStorage() {
  try {
    return uni.getStorageSync('auth')
  } catch (e) {
    return null
  }
}

export default {
  // Getters read storage first (cross-page), fall back to state (current page).
  get token() {
    var info = readStorage()
    return (info && info.token) || state.token
  },
  get user() {
    var info = readStorage()
    return (info && info.user) || state.user
  },

  // isLogin must check storage — state is page-local in mini program.
  isLogin: function () {
    var info = readStorage()
    return !!(info && info.token)
  },

  init: function () {
    var info = readStorage()
    if (info) {
      state.token = info.token
      state.user = info.user
    }
  },

  save: function (_token, _user) {
    state.token = _token
    state.user = _user
    try {
      uni.setStorageSync('auth', { token: _token, user: _user })
    } catch (e) {
      console.log('setStorageSync error:', e)
    }
  },

  logout: function () {
    state.token = ''
    state.user = null
    try {
      uni.removeStorageSync('auth')
    } catch (e) {
      console.log('removeStorageSync error:', e)
    }
  }
}
