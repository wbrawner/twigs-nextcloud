import Vue from 'vue'
import Vuex from 'vuex'
import axios from '@nextcloud/axios'
import router from '../router'

Vue.use(Vuex)

export default new Vuex.Store({
    state: {
        budgets: [],
        budgetBalances: {},
        currentBudget: 0,
        categories: [],
        categoryBalances: {},
        currentCategory: 0,
        transactions: [],
        currentTransaction: 0,
    },
    getters: {
        budgets: (state) => state.budgets,
        budget: (state) => (id) => state.budgets.find(budget => budget.id === id),
        budgetBalance: (state) => (id) => state.budgetBalances[id],
        categories: (state) => state.categories,
        category: (state) => (id) => {
            return state.categories.find(category => category.id === id)
        },
        categoryBalance: (state) => (categoryId) => {
            return state.categoryBalances[categoryId];
        },
        categoryRemainingBalance: (state, getters) => (category) => {
            const modifier = category.expense ? -1 : 1;
            return category.amount - (getters.categoryBalance(category.id) * modifier);
        },
        transactions: (state) => state.transactions,
        transaction: (state) => state.transactions.find(transaction => transaction.id === state.currentTransaction),
    },
    actions: {
        budgetListViewed({ commit }) {
            axios.get(OC.generateUrl('/apps/twigs/api/v1.0/budgets'))
                .then(function (response) {
                    commit('setBudgets', response.data)
                    response.data.forEach(budget => {
                        axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions/sum?budgetId=${budget.id}`))
                            .then(function (response) {
                                commit({
                                    type: 'setBudgetBalance',
                                    ...response.data
                                })
                            })
                    })
                })
        },
        budgetClicked({ commit }, budgetId) {
            router.push({ name: "budgetDetails", params: { id: budgetId } })
        },
        budgetDetailsViewed({ commit }, budgetId) {
            commit('setCurrentBudget', budgetId)
            commit('setCategories', [])
            commit('setTransactions', [])
            commit('setCurrentCategory', undefined)
            axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/categories?budgetId=${budgetId}`))
                .then(function (response) {
                    commit('setCategories', response.data)
                    response.data.forEach(category => {
                        axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions/sum?categoryId=${category.id}`))
                            .then(function (response) {
                                commit({
                                    type: 'setCategoryBalance',
                                    ...response.data
                                })
                            })
                    });
                })
            axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions?budgetId=${budgetId}?count=10`))
                .then((response) => commit('setTransactions', response.data))
        },
        categoryClicked({ commit }, categoryId) {
            router.push({ name: "categoryDetails", params: { id: categoryId } })
        },
        categoryDetailsViewed({ commit, state }, categoryId) {
            commit('setCurrentCategory', categoryId)
            if (state.categories.length === 0) {
                axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/categories/${categoryId}`))
                    .then((response) => {
                        commit('setCategories', [response.data])
                    })
            }
            axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions?categoryId=${categoryId}`))
                .then((response) => commit('setTransactions', response.data))
        },
        addTransactionClicked({ commit }) {
            router.push({ name: "newTransaction" })
        },
        addEditTransactionViewed({ commit, state, getters }, transactionId) {
            if (transactionId && getters.transaction(transactionId) === undefined) {
                axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions/${transactionId}`))
                    .then((response) => {
                        commit('setTransactions', [response.data])
                    })
            }
        },
        addEditTransactionBudgetSelected({ commit, state }, budgetId) {
            commit('setCategories', [])
            if (!budgetId) return;
            axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/categories?budgetId=${budgetId}`))
                .then(function (response) {
                    commit('setCategories', response.data)
                })
        },
        addEditTransactionSaveClicked({ commit }, transaction) {
            let request;
            if (transaction.id) {
                request = axios.put(OC.generateUrl(`/apps/twigs/api/v1.0/transactions/${transaction.id}`), transaction)
            } else {
                request = axios.post(OC.generateUrl(`/apps/twigs/api/v1.0/transactions`), transaction)
            }
            request.then(response => {
                commit('addTransaction', response.data)
                router.push({ name: "transactionDetails", params: { id: response.data.id } })
            })
        },
        transactionClicked({ commit }, transactionId) {
            router.push({ name: "transactionDetails", params: { id: transactionId } })
        },
        transactionDetailsViewed({ commit, state }, transactionId) {
            commit('setCurrentTransaction', transactionId)

            if (state.transactions.length === 0) {
                axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions/${transactionId}`))
                    .then((response) => {
                        commit('setTransactions', [response.data])
                        if (state.categories.length === 0) {
                            axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/categories?budgetId=${response.data.budgetId}`))
                                .then(function (response) {
                                    commit('setCategories', response.data)
                                    response.data.forEach(category => {
                                        axios.get(OC.generateUrl(`/apps/twigs/api/v1.0/transactions/sum?categoryId=${category.id}`))
                                            .then(function (response) {
                                                commit({
                                                    type: 'setCategoryBalance',
                                                    ...response.data
                                                })
                                            })
                                    });
                                })
                        }
                    })
            }
        },
    },
    mutations: {
        setCurrentBudget(state, budgetId) {
            state.currentBudget = Number.parseInt(budgetId)
        },
        setBudgetBalance(state, data) {
            state.budgetBalances = {
                ...state.budgetBalances,
                [data.budgetId]: data.sum
            }
        },
        setBudgets(state, budgets) {
            state.budgets = budgets
        },
        setCurrentCategory(state, categoryId) {
            state.currentCategory = Number.parseInt(categoryId)
        },
        setCategories(state, data) {
            state.categories = data
        },
        setCategoryBalance(state, data) {
            state.categoryBalances = {
                ...state.categoryBalances,
                [data.categoryId]: data.sum
            }
        },
        addTransaction(state, transaction) {
            state.transactions = [
                ...state.transactions.filter(t => t.id !== transaction.id),
                transaction
            ]
        },
        setTransactions(state, data) {
            state.transactions = data
        },
        setCurrentTransaction(state, transactionId) {
            state.currentTransaction = Number.parseInt(transactionId)
        },
    }
})
