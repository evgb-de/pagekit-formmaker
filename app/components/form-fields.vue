<template>

    <div>
        <div class="uk-alert" v-show="!formitem.id">{{ 'Save form before adding fields.' | trans }}</div>

        <div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin v-show="formitem.id">
            <div class="uk-flex uk-flex-middle uk-flex-wrap" data-uk-margin>

                <div class="uk-margin-left" v-show="selected.length">
                    <ul class="uk-subnav pk-subnav-icon">
                        <li><a class="pk-icon-delete pk-icon-hover" title="{{ 'Delete' | trans }}"
                               data-uk-tooltip="{delay: 500}" @click="removeFields"
                               v-confirm="'Delete field?' | trans"></a>
                        </li>
                    </ul>
                </div>

            </div>
            <div class="uk-position-relative" data-uk-margin>

                <div data-uk-dropdown="{ mode: 'click' }">
                    <a class="uk-button uk-button-primary">{{ 'Add Field' | trans
                    }}</a>

                    <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                        <ul class="uk-nav uk-nav-dropdown">
                            <li v-for="type in types | orderBy 'label'">
                            <a @click.prevent="$root.editFormField(type.id)">{{ type.label }}</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="uk-overflow-container">

            <div class="pk-table-fake pk-table-fake-header"
                 :class="{'pk-table-fake-border': !fields || !fields.length}">
                <div class="pk-table-width-minimum pk-table-fake-nestable-padding">
                    <input type="checkbox" v-check-all:selected.literal="input[name=id]">
                </div>
                <div class="pk-table-min-width-100">{{ 'Label' | trans }}</div>
                <div class="pk-table-width-100 uk-text-center">{{ 'Required' | trans }}</div>
                <div class="pk-table-width-150">{{ 'Type' | trans }}</div>
            </div>

            <ul class="uk-nestable uk-margin-remove" v-el:nestable v-show="fields.length">
                <form-field v-for="field in fields | orderBy 'priority'" :field="field"></form-field>

            </ul>

        </div>

        <h3 class="uk-h1 uk-text-muted uk-text-center" v-show="fields && !fields.length">{{ 'No fields found.' | trans
        }}</h3>

        <script id="field" type="text/template">
            <li class="uk-nestable-item" :class="{'uk-active': $parent.isSelected(field)}" data-id="{{ field.id }}">

            <div class="uk-nestable-panel pk-table-fake uk-form uk-visible-hover">
            <div class="pk-table-width-minimum pk-table-collapse">
            <div class="uk-nestable-toggle" data-nestable-action="toggle"></div>
            </div>
            <div class="pk-table-width-minimum"><input type="checkbox" name="id" value="{{ field.id }}"
            @click="toggleSelect(field)"></div>
            <div class="pk-table-width-minimum"><span>{{ field.page }}</span></div>
            <div class="pk-table-min-width-100">
            <a v-if="type" @click.prevent="$root.editFormField(field.id)">{{ field.label }}</a>
            <span v-else>{{ field.label }}</span>
            <br/><small class="uk-text-muted">{{ field.slug }}</small>
            </div>
            <div class="pk-table-width-100 uk-text-center">
            <td class="uk-text-center">
            <a :class="{'pk-icon-circle-danger': !field.data.required, 'pk-icon-circle-success': field.data.required}"
            @click.prevent="$parent.toggleRequired(field)"></a>
            </td>
            </div>
            <div class="pk-table-width-150 pk-table-max-width-150 uk-text-truncate">
            <span v-if="type">{{ type.label }}</span>
            <span v-else class="uk-text-danger">{{ field.type }}: {{ 'type not found!' | trans }}</span>
            </div>
            </div>


            </li>

        </script>
    </div>


</template>

<script>
/*global _, UIkit */

export default {

    name: 'FormFields',

    components: {
        'form-field': {

            name: 'FormField',

            props: {'field': Object,},

            template: '#field',

            computed: {
                type() {
                    return this.$parent.getFieldType(this.field);
                },
            },
        },
    },

    mixins: [window.BixieFieldtypes,],

    props: {'formitem': Object, 'types': Object, 'form': Object,},

    data: () => ({
        selected: [],
        editid: '',
    }),

    created() {
        this.Fields = this.$resource('api/formmaker/field{/id}');
        this.load();
    },

    ready() {

        UIkit.nestable(this.$els.nestable, {
            maxDepth: 20,
            group: 'formmaker.fields',
        }).on('change.uk.nestable', (e, nestable, el, type) => {

            if (type && type !== 'removed') {

                this.Fields.save({id: 'updateOrder',}, {
                    fields: nestable.list(),
                }).then(this.load, () => this.$notify('Reorder failed.', 'danger'));
            }
        });

    },

    methods: {

        load() {
            return this.Fields.query({form_id: this.formitem.id,}).then(res => {
                this.$set('fields', res.data);
                this.$set('selected', []);
            });
        },

        toggleRequired(field) {

            field.data.required = field.data.required ? 0 : 1;

            this.Fields.save({id: field.id,}, {field: field,}).then(() => {
                this.load();
                this.$notify('Field saved.');
            }, res => {
                this.load();
                this.$notify(res.data, 'danger');
            });
        },

        getSelected() {
            return this.fields.filter(field => this.isSelected(field));
        },

        isSelected(field) {
            return this.selected.indexOf(field.id.toString()) !== -1;
        },

        toggleSelect(field) {

            const index = this.selected.indexOf(field.id.toString());

            if (index === -1) {
                this.selected.push(field.id.toString());
            } else {
                this.selected.splice(index, 1);
            }
        },

        getFieldType(field) {
            return _.find(this.types, 'id', field.type);
        },

        removeFields() {
            this.Fields.delete({id: 'bulk',}, {ids: this.selected,}).then(() => {
                this.load();
                this.$notify('Field(s) deleted.');
            });
        },

    },

};

</script>
