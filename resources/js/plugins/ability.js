import { defineAbility } from '@casl/ability';

export default defineAbility((can, cannot) => {
    let permissions = [];
    permissions = JSON.parse(localStorage.getItem('user_permissions'));
    if(permissions){
        for (let index = 0; index < permissions.length; index++) {
            const element = permissions[index];
            can(element.name, 'all');
        }
    }
});