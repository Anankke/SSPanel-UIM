export default {
    store: tmp,
    mixins: [mutationMap, methodsMixin],
    computed: Vuex.mapState({
        msgrCon: 'msgrCon',
        modalCon: 'modalCon',
        globalConfig: 'globalConfig',
        logintoken: 'logintoken',
        isLoading: 'isLoading',
        userCon: state => state.userState.userCon,
        userSettings: state => state.userState.userSettings,
    }),
    methods: Vuex.mapActions({
        callMsgr: 'CALL_MSGR',
        callModal: 'CALL_MODAL',
    }),

}