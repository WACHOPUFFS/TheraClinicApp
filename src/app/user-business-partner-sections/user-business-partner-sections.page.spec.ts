import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UserBusinessPartnerSectionsPage } from './user-business-partner-sections.page';

describe('UserBusinessPartnerSectionsPage', () => {
  let component: UserBusinessPartnerSectionsPage;
  let fixture: ComponentFixture<UserBusinessPartnerSectionsPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(UserBusinessPartnerSectionsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
