import Vue from 'vue'
import Vuex from 'vuex'
import axios from '@nextcloud/axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        budgets: [],
        currentBudget: 0,
        categories: [],
        currentCategory: 0,
        transactions: [],
        currentTransaction: 0
    },
    getters: {
        budgets: (state) => {
            if (state.budgets.length === 0) {
                axios.get(OC.generateUrl("/apps/twigs/api/v1.0/budgets"))
                    .then(function (response) {
                        state.budgets = response.data;
                    })
            }
            return state.budgets
        },
        budget: (state) => (id) => {
            return state.budgets.find(budget => budget.id === id)
        }
    }
}) 