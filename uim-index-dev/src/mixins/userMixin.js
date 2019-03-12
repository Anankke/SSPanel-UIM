import {
  _get
} from '../js/fetch'

export default {
  props: ['annC', 'baseURL'],
  methods: {
    reConfigResourse() {
      _get('/getallresourse', 'include').then((r) => {
        if (r.ret === 1) {
          this.updateUserSet(r.resourse)
        } else {
          this.ajaxNotLogin()
        }
      })
    },
    updateUserSet(resourse) {
      this.setAllResourse(resourse)
    }
  }
}
