import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { UserBusinessPartnerSectionsPage } from './user-business-partner-sections.page';

const routes: Routes = [
  {
    path: '',
    component: UserBusinessPartnerSectionsPage
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class UserBusinessPartnerSectionsPageRoutingModule {}
