export default {
  props: ['resourseTrans'],
  methods: {
    wheelChange (e) {
      this.$emit('turnPageByWheel', e.deltaY)
    }
  }
}
