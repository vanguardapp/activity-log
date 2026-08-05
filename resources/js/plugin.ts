import '../css/plugin.css';

import ActivityWidget from './components/ActivityWidget.vue';
import RecentActivity from './components/RecentActivity.vue';
import Index from './pages/Index.vue';

/**
 * Register everything this plugin contributes to the host.
 *
 * Names are namespaced with the plugin slug and must match what the PHP side
 * returns from Slot::component(), Widget::component() and Inertia::render().
 */
window.Vanguard.plugin('user-activity', (vanguard) => {
    vanguard.page('user-activity::Index', Index);
    vanguard.slot('user-activity::RecentActivity', RecentActivity);
    vanguard.widget('user-activity::ActivityWidget', ActivityWidget);
});
