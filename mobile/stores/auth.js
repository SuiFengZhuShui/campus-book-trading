import Vue from 'vue'

var state = Vue.observable({
  user: null,
  token: ''
})

export default {
  get user() { return state.user },
  get token() { return state.token },
  isLogin: function () { return !!state.token },
  init: function () {
    try {
      var info = uni.getStorageSync('auth')
      if (info) {
        state.user = info.user
        state.token = info.token
      }
    } catch (e) {
      console.log('auth init error:', e)
    }
  },
  save: function (_token, _user) {
    state.token = _token
    state.user = _user
    uni.setStorageSync('auth', { token: _token, user: _user })
  },
  logout: function () {
    state.token = ''
    state.user = null
    uni.removeStorageSync('auth')
  }
}
