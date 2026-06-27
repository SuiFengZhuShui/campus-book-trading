<template>
  <image v-if="localSrc" :src="localSrc" :mode="mode" @click="$emit('click')" @error="onError" />
  <view v-else class="ph" @click="$emit('click')">
    <text class="ph-icon">📖</text>
  </view>
</template>

<script>
// #ifdef MP-WEIXIN
var BASE = 'http://127.0.0.1'
// #endif
// #ifndef MP-WEIXIN
var BASE = ''
// #endif

export default {
  props: {
    src: { type: String, default: '' },
    mode: { type: String, default: 'aspectFill' }
  },
  data() {
    return { localSrc: '' }
  },
  mounted() {
    this.loadImage()
  },
  watch: {
    src: function () { this.loadImage() }
  },
  methods: {
    loadImage: function () {
      var self = this
      if (!this.src) return
      var url = this.src
      // 相对路径 -> 拼接 BASE_URL 后下载
      if (url.indexOf('/') === 0) {
        url = BASE + url
      }
      // 远程 HTTP URL -> 需要下载到本地才能在 image 组件显示
      // #ifdef MP-WEIXIN
      if (url.indexOf('http') === 0) {
        uni.downloadFile({
          url: url,
          success: function (res) {
            if (res.statusCode === 200) {
              self.localSrc = res.tempFilePath
            }
          },
          fail: function () {
            self.localSrc = ''
          }
        })
      } else {
        // 已经是本地文件路径
        self.localSrc = url
      }
      // #endif
      // #ifndef MP-WEIXIN
      self.localSrc = url
      // #endif
    },
    onError: function () {
      this.localSrc = ''
    }
  }
}
</script>

<style scoped>
image { width: 100%; height: 100%; }
.ph { width: 100%; height: 100%; background: #e5dccf; display: flex; align-items: center; justify-content: center; }
.ph-icon { font-size: 28px; color: #d0cdc8; }
</style>
