export default {
    delimiters: ['$[',']$'],
    props: ['annC','baseURL'],
    methods: {
        reConfigResourse() {
            _get('/getallresourse','include').then((r)=>{
                console.log(r);
                this.updateUserSet(r.resourse);
            });
        },
        updateUserSet(resourse) {
            this.setAllResourse(resourse);
        },
    }
}