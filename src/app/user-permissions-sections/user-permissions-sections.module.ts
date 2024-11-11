import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { UserPermissionsSectionsPageRoutingModule } from './user-permissions-sections-routing.module';

import { UserPermissionsSectionsPage } from './user-permissions-sections.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    UserPermissionsSectionsPageRoutingModule
  ],
  declarations: [UserPermissionsSectionsPage]
})
export class UserPermissionsSectionsPageModule {}
