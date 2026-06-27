<script>
import auth from '@/stores/auth.js'

export default {
  onLaunch: function () {
    auth.init()
    // #ifdef H5
    this.$nextTick(function () {
      initAurora()
    })
    // #endif
  }
}

// #ifdef H5
function initAurora() {
  // 创建极光 canvas
  var ac = document.createElement('canvas')
  ac.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;'
  document.body.appendChild(ac)

  var sc = document.createElement('canvas')
  sc.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;'
  document.body.appendChild(sc)

  var actx = ac.getContext('2d')
  var sctx = sc.getContext('2d')
  var W, H, t = 0, mx = 0.5, my = 0.5

  function resize() {
    W = ac.width = sc.width = window.innerWidth
    H = ac.height = sc.height = window.innerHeight
  }
  resize()
  window.addEventListener('resize', resize)

  document.addEventListener('mousemove', function (e) {
    mx = e.clientX / W; my = e.clientY / H
  })
  document.addEventListener('touchmove', function (e) {
    mx = e.touches[0].clientX / W; my = e.touches[0].clientY / H
  }, { passive: true })

  var sparkles = []
  for (var i = 0; i < 30; i++) {
    sparkles.push({
      x: Math.random() * W, y: Math.random() * H,
      r: 0.5 + Math.random() * 1.5,
      vx: (Math.random() - 0.5) * 0.15,
      vy: (Math.random() - 0.5) * 0.15 - 0.08,
      alpha: 0.25 + Math.random() * 0.4,
      phase: Math.random() * Math.PI * 2
    })
  }

  function draw() {
    t += 0.003
    var px = mx, py = my

    actx.clearRect(0, 0, W, H)

    var bands = [
      { baseY: H * 0.20, r: 180, g: 148, b: 80,  r2: 212, g2: 188, b2: 124, h: 150, sp: 0.7,  amp: 50, s: 1.0 },
      { baseY: H * 0.38, r: 212, g: 188, b: 124, r2: 180, g2: 148, b2: 80,  h: 130, sp: 0.55, amp: 55, s: 0.85 },
      { baseY: H * 0.52, r: 107, g: 39,  b: 55,  r2: 139, g2: 58,  b2: 74,  h: 110, sp: 0.6,  amp: 45, s: 0.7 },
      { baseY: H * 0.68, r: 74,  g: 103, b: 65,  r2: 90,  g2: 125, b2: 81,  h: 120, sp: 0.5,  amp: 40, s: 0.65 },
      { baseY: H * 0.84, r: 139, g: 58,  b: 74,  r2: 107, g2: 39,  b2: 55,  h: 90,  sp: 0.45, amp: 30, s: 0.5 }
    ]

    bands.forEach(function (b) {
      var oy = (py - 0.5) * 25 * b.s
      var ox = (px - 0.5) * 18 * b.s
      var by = b.baseY + oy

      var grad = actx.createLinearGradient(0, by - b.h, 0, by + b.h)
      grad.addColorStop(0, 'rgba(' + b.r + ',' + b.g + ',' + b.b + ',0)')
      grad.addColorStop(0.25, 'rgba(' + b.r + ',' + b.g + ',' + b.b + ',0.22)')
      grad.addColorStop(0.5, 'rgba(' + b.r2 + ',' + b.g2 + ',' + b.b2 + ',0.13)')
      grad.addColorStop(0.75, 'rgba(' + b.r + ',' + b.g + ',' + b.b + ',0.04)')
      grad.addColorStop(1, 'rgba(' + b.r + ',' + b.g + ',' + b.b + ',0)')

      actx.fillStyle = grad
      actx.beginPath()
      actx.moveTo(-50, by)
      for (var x = -50; x <= W + 50; x += 5) {
        var y = by
          + Math.sin(x * 0.002 + t * b.sp + b.baseY * 0.01 + ox * 0.01) * b.amp * b.s
          + Math.sin(x * 0.005 + t * b.sp * 1.4 + b.baseY * 0.02) * b.amp * 0.55 * b.s
          + Math.sin(x * 0.009 + t * b.sp * 0.6) * b.amp * 0.3 * b.s
          + Math.sin(x * 0.018 + t * b.sp * 2.1) * b.amp * 0.18 * b.s
        actx.lineTo(x, y)
      }
      actx.lineTo(W + 50, by + b.h + 80)
      actx.lineTo(-50, by + b.h + 80)
      actx.closePath()
      actx.fill()
    })

    sctx.clearRect(0, 0, W, H)
    sparkles.forEach(function (s) {
      s.x += s.vx + Math.sin(t * 3 + s.phase) * 0.08
      s.y += s.vy + Math.cos(t * 2.5 + s.phase) * 0.06
      if (s.x < -10) s.x = W + 10
      if (s.x > W + 10) s.x = -10
      if (s.y < -10) s.y = H + 10
      if (s.y > H + 10) s.y = -10

      var pulse = 0.4 + 0.6 * Math.sin(t * 2 + s.phase)
      var alpha = s.alpha * pulse

      sctx.beginPath()
      sctx.arc(s.x, s.y, s.r, 0, Math.PI * 2)
      sctx.fillStyle = 'rgba(180,148,80,' + alpha + ')'
      sctx.fill()
      sctx.beginPath()
      sctx.arc(s.x, s.y, s.r * 2.5, 0, Math.PI * 2)
      sctx.fillStyle = 'rgba(180,148,80,' + (alpha * 0.15) + ')'
      sctx.fill()
    })

    requestAnimationFrame(draw)
  }
  draw()
}
// #endif
</script>

<style lang="scss">
@import '@/uni.scss';
</style>
