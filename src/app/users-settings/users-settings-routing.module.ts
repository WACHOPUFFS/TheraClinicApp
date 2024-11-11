import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { UsersSettingsPage } from './users-settings.page';

const routes: Routes = [
  {
    path: '',
    component: UsersSettingsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class UsersSettingsPageRoutingModule {}
