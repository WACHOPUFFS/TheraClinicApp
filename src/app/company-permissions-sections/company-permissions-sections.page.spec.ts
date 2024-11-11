import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CompanyPermissionsSectionsPage } from './company-permissions-sections.page';

describe('CompanyPermissionsSectionsPage', () => {
  let component: CompanyPermissionsSectionsPage;
  let fixture: ComponentFixture<CompanyPermissionsSectionsPage>;

  beforeEach(async(() => {
    fixture = TestBed.createComponent(CompanyPermissionsSectionsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  }));

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
