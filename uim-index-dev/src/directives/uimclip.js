export default {
  inserted: function (el, binding) {
    el.addEventListener('click', (e) => {
      let copy = new Promise((resolve, reject) => {
        let input = document.createElement('input')
        let body = document.getElementsByTagName('body')[0]
        let value = el.dataset.uimclip
        input.setAttribute('type', 'text')
        input.setAttribute('value', value)
        body.appendChild(input)
        input.focus()
        input.setSelectionRange(0, value.length)
        document.execCommand('copy', true)
        resolve(input)
      })
      copy.then((r) => {
        r.remove()
        binding.value.onSuccess()
      })
    })
  }
}
