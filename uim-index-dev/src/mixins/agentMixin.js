export default {
  computed: {
    suburlBase: function () {
      return this.subUrl + this.ssrSubToken
    },
    suburlMu0: function () {
      return this.suburlBase + '?mu=0'
    },
    suburlMu1: function () {
      return this.suburlBase + '?mu=1'
    },
    suburlMu3: function () {
      return this.suburlBase + '?mu=3'
    },
    suburlMu2: function () {
      return this.suburlBase + '?mu=2'
    }
  },
  data: function () {
    return {
      downloads: [{
        type: 'SSR',
        agent: {
          Windows: [{
            agentName: 'SSR',
            href: '/ssr-download/ssr-win.7z',
            id: 'AGENT_1_1_1'
          },
          {
            agentName: 'SSTAP',
            href: '/ssr-download/SSTap.7z',
            id: 'AGENT_1_1_2'
          }
          ],
          Macos: [{
            agentName: 'SSX',
            href: '/ssr-download/ssr-mac.dmg',
            id: 'AGENT_1_2_1'
          }],
          Linux: [{
            agentName: 'SS-qt5',
            href: '#',
            id: 'AGENT_1_3_1'
          }],
          Ios: [{
            agentName: 'Potatso Lite',
            href: '#',
            id: 'AGENT_1_4_1'
          },
          {
            agentName: 'Shadowrocket',
            href: '#',
            id: 'AGENT_1_4_2'
          }
          ],
          Android: [{
            agentName: 'SSR',
            href: '/ssr-download/ssr-android.apk',
            id: 'AGENT_1_5_1'
          },
          {
            agentName: 'SSRR',
            href: '/ssr-download/ssrr-android.apk',
            id: 'AGENT_1_5_2'
          }
          ],
          Router: [{
            agentName: 'FancySS',
            href: 'https://github.com/hq450/fancyss_history_package',
            id: 'AGENT_1_6_1'
          }]
        }
      },
      {
        type: 'SS/SSD',
        agent: {
          Windows: [{
            agentName: 'SSD',
            href: '/ssr-download/ssd-win.7z',
            id: 'AGENT_2_1_1'
          }],
          Macos: [{
            agentName: 'SSXG',
            href: '/ssr-download/ss-mac.zip',
            id: 'AGENT_2_2_1'
          }],
          Linux: [{
            agentName: '/',
            href: '#',
            id: 'AGENT_2_3_1'
          }],
          Ios: [{
            agentName: 'Potatso Lite',
            href: '#',
            id: 'AGENT_2_4_1'
          },
          {
            agentName: 'Shadowrocket',
            href: '#',
            id: 'AGENT_2_4_2'
          }
          ],
          Android: [{
            agentName: 'SSD',
            href: '/ssr-download/ssd-android.apk',
            id: 'AGENT_2_5_1'
          },
          {
            agentName: '混淆插件',
            href: '/ssr-download/ss-android-obfs.apk',
            id: 'AGENT_2_5_2'
          }
          ],
          Router: [{
            agentName: 'FancySS',
            href: 'https://github.com/hq450/fancyss_history_package',
            id: 'AGENT_2_6_1'
          }]
        }
      },
      {
        type: 'V2RAY',
        agent: {
          Windows: [{
            agentName: 'V2RayN',
            href: '/ssr-download/v2rayn.zip',
            id: 'AGENT_3_1_1'
          }, {
            agentName: 'ClashX',
            href: '/ssr-download/Clash-Windows.7z',
            id: 'AGENT_3_1_2'
          }],
          Macos: [{
            agentName: '/',
            href: '#',
            id: 'AGENT_3_2_1'
          }],
          Linux: [{
            agentName: '/',
            href: '#',
            id: 'AGENT_3_3_1'
          }],
          Ios: [{
            agentName: 'Shadowrocket',
            href: '#',
            id: 'AGENT_3_4_1'
          }],
          Android: [{
            agentName: 'V2RayN',
            href: '/ssr-download/v2rayng.apk',
            id: 'AGENT_3_5_1'
          }],
          Router: [{
            agentName: 'FancySS',
            href: 'https://github.com/hq450/fancyss_history_package',
            id: 'AGENT_3_6_1'
          }]
        }
      }
      ]
    }
  },
  methods: {
    changeAgentType (e) {
      this.setCurrentDlType(e.target.dataset.type)
    }
  }
}
