import { mapActions } from 'vuex'
import { mapState } from 'vuex'
import { mapMutations } from 'vuex'

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
        currentAgentType: state => state.userState.currentAgentType,
    }),
    methods: {
        ...mapActions({
            callMsgr: 'CALL_MSGR',
            callModal: 'CALL_MODAL',
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
            setCurrentAgentType: 'SET_CURRENT_AGENT_TYPE',
        }),
        successCopied() {
            let callConfig = {
                msg: '复制成功！,已将链接复制到剪贴板',
                icon: 'fa-check-square-o',
                time: '1500',
            }
            this.callMsgr(callConfig);
        },
    },

}