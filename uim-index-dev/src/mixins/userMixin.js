import { _get } from '../js/fetch'

export default {
  props: ['annC', 'baseURL'],
  methods: {
    reConfigResourse () {
      _get('/getallresourse', 'include').then((r) => {
        window.console.log(r)
        this.updateUserSet(r.resourse)
      })
    },
    updateUserSet (resourse) {
      this.setAllResourse(resourse)
    }
  }
}
