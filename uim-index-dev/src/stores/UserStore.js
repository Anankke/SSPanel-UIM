import Vue from 'vue'

export default {
  state: {
    userCon: '',
    baseURL: "",
    mergeSub: "false",
    ssrSubToken: '',
    subUrl: '',
    iosAccount: '',
    iosPassword: '',
    displayIosClass: '',
    ann: {
      content: "",
      date: "",
      id: "",
      markdown: ""
    },
    sign: {
      isTrsfficRefreshed: false,
      isSignShow: false,
      transition: false
    },
    userSettings: {
      pages: [{
          id: 'user-resourse'
        },
        {
          id: 'user-settings'
        }
      ],
      currentPage: 'user-resourse',
      currentPageIndex: 0,
      resourse: [{
          name: '等级有效期',
          content: ''
        },
        {
          name: '账号有效期',
          content: ''
        },
        {
          name: '在线设备数',
          content: ''
        },
        {
          name: '端口速率',
          content: ''
        }
      ],
      tipsLink: [{
          name: '端口',
          content: ''
        },
        {
          name: '密码',
          content: ''
        },
        {
          name: '加密',
          content: ''
        },
        {
          name: '协议',
          content: ''
        },
        {
          name: '混淆',
          content: ''
        },
        {
          name: '混淆参数',
          content: ''
        }
      ]
    },
    currentDlType: 'SSR',
    currentPlantformType: 'WINDOWS'
  },
  mutations: {
    SET_USERCON(state, config) {
      state.userCon = config
    },
    SET_USERMONEY(state, number) {
      state.userCon.money = number
    },
    SET_INVITE_NUM(state, number) {
      state.userCon.invite_num = number
    },
    SET_USERSETTINGS(state, config) {
      state.userSettings.tipsLink[0].content = config.port
      state.userSettings.tipsLink[1].content = config.passwd
      state.userSettings.tipsLink[2].content = config.method
      state.userSettings.tipsLink[3].content = config.protocol
      state.userSettings.tipsLink[4].content = config.obfs
      state.userSettings.tipsLink[5].content = config.obfs_param
    },
    ADD_NEWUSERCON(state, config) {
      for (let key in config) {
        Vue.set(state.userCon, key, config[key])
      }
    },
    SET_RESOURSE(state, config) {
      state.userSettings.resourse[config.index].content = config.content
    },
    SET_ALLURESOURSE(state, config) {
      for (let key in config) {
        state.userCon[key] = config[key]
      }
    },
    SET_CURRENT_DL_TYPE(state, content) {
      state.currentDlType = content
    },
    SET_CURRENT_PLANTFORM_TYPE(state, content) {
      state.currentPlantformType = content
    },
    SET_ALLBASECON(state, config) {
      for (let key in config) {
        Vue.set(state, key, config[key])
      }
    },
    SET_SIGNSET(state, config) {
      for (let key in config) {
        state.sign[key] = config[key]
      }
    },
    SET_ANN(state, ann) {
      state.ann = ann
    },
    SET_BASEURL(state, url) {
      state.baseURL = url
    },
    SET_MERGESUB(state, substate) {
      state.mergeSub = substate
    }
  },
  actions: {
    TraffictransTrigger({
      dispatch,
      commit,
      state
    }, config) {
      commit('SET_SIGNSET', {
        isTrsfficRefreshed: true
      })
      window.setTimeout(() => {
        commit('SET_SIGNSET', {
          isTrsfficRefreshed: false
        })
      }, 300)
    }
  }
}
