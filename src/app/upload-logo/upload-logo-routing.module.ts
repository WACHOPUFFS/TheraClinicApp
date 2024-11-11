import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { UploadLogoPage } from './upload-logo.page';

const routes: Routes = [
  {
    path: '',
    component: UploadLogoPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class UploadLogoPageRoutingModule {}
