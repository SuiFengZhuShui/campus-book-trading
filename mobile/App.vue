<script>
import auth from '@/stores/auth.js'

export default {
  onLaunch: function () {
    auth.init()
  }
}
</script>

<style lang="scss">
@import '@/uni.scss';
</style>
