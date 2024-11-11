import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { IonicModule } from '@ionic/angular';

import { UserBusinessPartnerSectionsPageRoutingModule } from './user-business-partner-sections-routing.module';

import { UserBusinessPartnerSectionsPage } from './user-business-partner-sections.page';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    IonicModule,
    UserBusinessPartnerSectionsPageRoutingModule
  ],
  declarations: [UserBusinessPartnerSectionsPage]
})
export class UserBusinessPartnerSectionsPageModule {}
