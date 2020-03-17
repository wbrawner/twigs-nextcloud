<template>
    <CategoryForm :category="category" />
</template>
<script>
import { mapGetters } from 'vuex'
import CategoryForm from './CategoryForm'

export default {
    name: 'edit-category',
    components: {
        CategoryForm
    },
    computed: {
        ...mapGetters(['category'])
    },
    mounted() {
        this.$store.dispatch("editCategoryViewed", this.$route.params.id);
    }
}
</script>