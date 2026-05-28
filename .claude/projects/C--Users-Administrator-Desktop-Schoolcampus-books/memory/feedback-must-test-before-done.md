---
name: feedback-must-test-before-done
description: 改完代码后必须实测验证通过才能报告完成，禁止改完就让用户自己测
metadata:
  type: feedback
---

改完代码必须先自测验证，拿到证据（curl HTTP 200、浏览器截图、build 通过等）才能说"好了"。禁止改完代码直接报告完成。

**Why:** 2026-05-23 给5个 Model 加 SoftDeletes trait 后未验证直接报告完成，结果双花括号语法错误导致全站 500，用户打开首页才发现。这是第二次犯同样错误（2026-05-22 已有类似教训 [[feedback-test-before-claiming-done]]）。

**How to apply:** 任何代码修改后，必须执行至少一项验证才能报告完成：
- PHP 修改：`curl http://127.0.0.1/` 确认 HTTP 200，再加针对性 curl 测试改动点
- Vue 修改：`npm run build` 确认零错误 + Playwright 打开页面看渲染
- Blade 修改：curl 对应页面确认 HTTP 200 + DOM 内容正确
- 批量修改：改完反向 grep 旧值确认零残留

已配置 hooks 自动执行：
- PHP 编辑后：`php -l` 语法检查 + 首页 curl smoke test
- Vue 编辑后：`vue-tsc --noEmit` 类型检查
