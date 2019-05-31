export default {
  inserted: function (el, binding) {
    el.addEventListener('click', (e) => {
      let input = document.createElement('input')
      let value = el.dataset.uimclip
      input.setAttribute('type', 'text')
      input.setAttribute('value', value)
      el.after(input)
      input.focus()
      input.setSelectionRange(0, value.length)
      document.execCommand('copy')
      input.remove();
      binding.value.onSuccess();
    })
  }
}
