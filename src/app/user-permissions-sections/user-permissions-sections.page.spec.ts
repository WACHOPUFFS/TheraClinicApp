import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UserPermissionsSectionsPage } from './user-permissions-sections.page';

describe('UserPermissionsSectionsPage', () => {
  let component: UserPermissionsSectionsPage;
  let fixture: ComponentFixture<UserPermissionsSectionsPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(UserPermissionsSectionsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
