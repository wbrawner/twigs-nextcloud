<template>
    <TransactionForm :transaction="transaction" />
</template>
<script>
import { mapGetters } from 'vuex'
import TransactionForm from './TransactionForm'

export default {
    name: 'edit-transaction',
    components: {
        TransactionForm
    },
    computed: {
        ...mapGetters(['transaction'])
    },
    mounted() {
        this.$store.dispatch("editTransactionViewed", this.$route.params.id);
    }
}
</script>