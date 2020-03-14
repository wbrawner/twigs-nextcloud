import Vue from 'vue'
import Vuex from 'vuex'
import axios from '@nextcloud/axios'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        budgets: [],
        currentBudget: 0,
        categories: {},
        currentCategory: 0,
        transactions: [],
        currentTransaction: 0,
    },
    getters: {
        budgets: (state) => {
            return state.budgets
        },
        budget: (state) => (id) => {
            return state.budgets.find(budget => budget.id === id)
        },
        categories: (state) => (budgetId) => {
            return state.categories[budgetId]
        },
    },
    actions: {
        budgetListViewed({ commit }) {
            axios.get(OC.generateUrl('/apps/twigs/api/v1.0/budgets'))
                .then(function (response) {
                    commit('setBudgets', response.data)
                })
        },
        budgetDetailsViewed({ commit }, budgetId) {
            axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/categories?budgetId=${budgetId}`))
                .then(function (response) {
                    commit({
                        type: 'setCategories',
                        budgetId: budgetId, 
                        categories: response.data
                    })
                })
        }
    },
    mutations: {
        setBudgets(state, budgets) {
            state.budgets = budgets
        },
        setCategories(state, data) {
            state.categories = {
                ...state.categories,
                [data.budgetId]: data.categories
            }
        }
    }
})
