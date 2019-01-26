export default {
    methods: {
        successCopied() {
            let callConfig = {
                msg: '复制成功！,已将链接复制到剪贴板',
                icon: 'fa-check-square-o',
                time: '1500',
            }
            this.callMsgr(callConfig);
        },
    }
}