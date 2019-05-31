import {
  mapActions,
  mapState,
  mapMutations
} from 'vuex'

import tmp from '../store'

export default {
  store: tmp,
  computed: mapState({
    msgrCon: 'msgrCon',
    modalCon: 'modalCon',
    globalConfig: 'globalConfig',
    logintoken: 'logintoken',
    isLoading: 'isLoading',
    userCon: state => state.userState.userCon,
    userSettings: state => state.userState.userSettings,
    currentDlType: state => state.userState.currentDlType,
    currentPlantformType: state => state.userState.currentPlantformType,
    ssrSubToken: state => state.userState.ssrSubToken,
    subUrl: state => state.userState.subUrl,
    iosAccount: state => state.userState.iosAccount,
    iosPassword: state => state.userState.iosPassword,
    displayIosClass: state => state.userState.displayIosClass,
    sign: state => state.userState.sign,
    ann: state => state.userState.ann,
    baseURL: state => state.userState.baseURL,
    mergeSub: state => state.userState.mergeSub
  }),
  methods: {
    ...mapActions({
      callMsgr: 'CALL_MSGR',
      callModal: 'CALL_MODAL',
      TraffictransTrigger: 'TraffictransTrigger'
    }),
    ...mapMutations({
      setGlobalConfig: 'SET_GLOBALCONFIG',
      setHitokoto: 'SET_HITOKOTO',
      setJinRiShiCi: 'SET_JINRISHICI',
      setLoadState: 'SET_LOADSTATE',
      setLoginToken: 'SET_LOGINTOKEN',
      setUserMoney: 'SET_USERMONEY',
      setInviteNum: 'SET_INVITE_NUM',
      setReasourse: 'SET_RESOURSE',
      setUserCon: 'SET_USERCON',
      setUserSettings: 'SET_USERSETTINGS',
      addNewUserCon: 'ADD_NEWUSERCON',
      setAllResourse: 'SET_ALLURESOURSE',
      setCurrentDlType: 'SET_CURRENT_DL_TYPE',
      setCurrentPlantformType: 'SET_CURRENT_PLANTFORM_TYPE',
      setAllBaseCon: 'SET_ALLBASECON',
      setSignSet: 'SET_SIGNSET',
      setAnn: 'SET_ANN',
      setBaseUrl: 'SET_BASEURL',
      setMergeSub: 'SET_MERGESUB'
    }),
    successCopied() {
      let callConfig = {
        msg: '复制成功！已将链接复制到剪贴板',
        icon: 'check-circle',
        time: '1500'
      }
      this.callMsgr(callConfig)
    },
    ajaxNotLogin() {
      let callConfig = {
        msg: '登录超时，请重新登录',
        icon: "times-circle",
        time: 1500
      };
      this.callMsgr(callConfig);
      this.setLoginToken(false);
      this.$router.push("/");
    }
  }

}
