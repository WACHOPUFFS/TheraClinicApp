import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { UserPermissionsSectionsPage } from './user-permissions-sections.page';

const routes: Routes = [
  {
    path: '',
    component: UserPermissionsSectionsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class UserPermissionsSectionsPageRoutingModule {}
