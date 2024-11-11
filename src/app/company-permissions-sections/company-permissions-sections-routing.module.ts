import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { CompanyPermissionsSectionsPage } from './company-permissions-sections.page';

const routes: Routes = [
  {
    path: '',
    component: CompanyPermissionsSectionsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class CompanyPermissionsSectionsPageRoutingModule {}
