import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { CompanyPermissionsSectionsPageRoutingModule } from './company-permissions-sections-routing.module';

import { CompanyPermissionsSectionsPage } from './company-permissions-sections.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    CompanyPermissionsSectionsPageRoutingModule
  ],
  declarations: [CompanyPermissionsSectionsPage]
})
export class CompanyPermissionsSectionsPageModule {}
