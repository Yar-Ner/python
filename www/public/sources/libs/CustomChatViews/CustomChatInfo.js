export default class CustomSideBar extends chat.views.sidebar {
     init(view) {
         super.init(view);
         view.queryView("tabbar").removeOption("users");
     }
}