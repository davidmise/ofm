import { reactive } from 'vue';

const state = reactive({
    isLoading: false,
    loadingCount: 0
});

export const loadingStore = {
    state,

    show() {
        state.loadingCount++;
        state.isLoading = true;
    },

    hide() {
        state.loadingCount = Math.max(0, state.loadingCount - 1);
        if (state.loadingCount === 0) {
            state.isLoading = false;
        }
    },

    get isLoading() {
        return state.isLoading;
    }
};
