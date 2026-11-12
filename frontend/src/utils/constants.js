export const CITIES = [
  'Addis Ababa',
  'Bahir Dar',
  'Hawassa',
  'Adama',
  'Dessie',
  'Kombolcha',
  'Dire Dawa',
  'Mekelle',
  'Gondar',
  'Jimma',
  'Shashamane',
  'Arba Minch',
  'Hosaena',
]

export const PROPERTY_TYPES = [
  { value: 'apartment',   label: 'Apartment' },
  { value: 'house',       label: 'House' },
  { value: 'studio',      label: 'Studio' },
  { value: 'condominium', label: 'Condominium' },
  { value: 'commercial',  label: 'Commercial' },
  { value: 'office',      label: 'Office Space' },
]

export const FURNISHING_STATUSES = [
  { value: 'furnished',       label: 'Furnished' },
  { value: 'semi-furnished',  label: 'Semi-Furnished' },
  { value: 'unfurnished',     label: 'Unfurnished' },
]

export const PROPERTY_STATUSES = [
  { value: 'pending',     label: 'Pending',     class: 'badge-pending' },
  { value: 'approved',    label: 'Approved',    class: 'badge-approved' },
  { value: 'rejected',    label: 'Rejected',    class: 'badge-rejected' },
  { value: 'rented',      label: 'Rented',      class: 'badge-rented' },
  { value: 'unavailable', label: 'Unavailable', class: 'badge-unavailable' },
]

export const INQUIRY_STATUSES = [
  { value: 'pending',  label: 'Pending',  class: 'badge-pending' },
  { value: 'accepted', label: 'Accepted', class: 'badge-accepted' },
  { value: 'rejected', label: 'Rejected', class: 'badge-rejected' },
]

export const SORT_OPTIONS = [
  { value: 'newest',     label: 'Newest First' },
  { value: 'price_asc',  label: 'Price: Low to High' },
  { value: 'price_desc', label: 'Price: High to Low' },
]

export const BEDROOM_OPTIONS = [
  { value: '', label: 'Any' },
  { value: '1', label: '1+' },
  { value: '2', label: '2+' },
  { value: '3', label: '3+' },
  { value: '4', label: '4+' },
]

// Icon names from lucide for property types
export const PROPERTY_TYPE_ICONS = {
  apartment:   'Building2',
  house:       'Home',
  studio:      'LayoutDashboard',
  condominium: 'Building',
  commercial:  'Store',
  office:      'Briefcase',
}

export const CURRENCY = 'ETB'

export const API_BASE = '/api'
