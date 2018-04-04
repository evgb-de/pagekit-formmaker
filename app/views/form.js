/*global _, Vue*/

// @vue/component
const vm = {

    el: '#formmaker-form',

    name: 'FormmakerForm',

    components: {
        recaptcha: require('../components/recaptcha.vue'),
    },

    data: () => _.assign({
        currentPage: 0,
        formitem: {},
        fields: [],
        message: '',
        error: '',
        thankyou: '',
        submission: {},
        form: {},
    }, window.$formmaker),

    methods: {

        save() {

            let data = {submission: this.submission,};

            this.$set('message', '');
            this.$set('error', '');

            this.$broadcast('submit', data);

            this.$http.post('api/formmaker/submission', data)
                .then(res => {
                    data = res.data;
                    this.message = data.message;
                    if (data.submission.thankyou) {
                        this.$set('thankyou', data.submission.thankyou);
                    }
                    if (this.formitem.data.google_datalayer) {
                        const dataLayer = window.dataLayer || [];
                        dataLayer.push({'event': 'form-' + this.formitem.slug,});
                    }
                    if (data.submission.redirect) {
                        window.location.replace(data.submission.redirect);
                    }
                }, res => this.error = this.$trans(res.data.message || res.data));
        },
        back() {
            window.$formmaker.currentPage -= 1;
        },
        forth() {
            window.$formmaker.currentPage += 1;
        },

    },

};

Vue.ready(() => {
    window.Formmaker = new Vue(vm);
});
export default vm;