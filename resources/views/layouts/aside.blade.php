<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
      <img src="{{ asset('backend/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('backend/dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }}</a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-item has-treeview {{ Request::segment(1) =='users' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' ? 'menu-open' : null }}">
            <a href="#" class="nav-link {{ Request::segment(1) =='users' || Request::segment(1) == 'roles' || Request::segment(1) == 'permissions' ? 'active' : null }}">
              <i class="nav-icon far fa-plus-square"></i>
              <p>
                Quản lý người dùng
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ Request::segment(1) =='users' ? 'active' : null }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Người dùng</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link {{ Request::segment(1) =='roles' ? 'active' : null }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Vai trò</p>
                </a>
              </li>
              <li class="nav-item">
              @role('Super-Admin')
                <a href="{{ route('permissions.index') }}" class="nav-link {{ Request::segment(1) =='permissions' ? 'active' : null }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Quyền</p>
                </a>
              @endrole
              </li>
            </ul>
          </li>



        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
