import { reactive } from "vue";

export const followState = reactive({
    followStatus: {},
    setFollowStatus(userId, isFollowing) {
        this.followStatus[userId] = isFollowing;
    },
    getFollowStatus(userId) {
        return this.followStatus[userId] || false;
    },
});
