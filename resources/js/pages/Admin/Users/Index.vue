<template>
  <AdminLayout>
    <Head title="User Management - Admin" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-black">User Management</h1>
          <p class="text-gray-600 mt-1 text-sm sm:text-base">Kelola pengguna sistem</p>
        </div>
        <button
          @click="showCreateModal = true"
          class="inline-flex items-center justify-center px-4 py-2.5 !bg-black !text-white text-sm font-medium rounded-lg hover:!bg-gray-800 transition-colors shadow-sm border-2 border-black cursor-pointer"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Tambah User
        </button>
      </div>

      <!-- Filters -->
      <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 shadow-sm">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Search -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Cari User</label>
            <div class="relative">
              <input
                type="text"
                v-model="searchForm.search"
                placeholder="Nama atau email..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-400 focus:border-gray-400 transition-all"
              />
              <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>

          <!-- Role Filter -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Filter Role</label>
            <div class="relative">
              <select
                v-model="searchForm.role_filter"
                class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition-all bg-white cursor-pointer appearance-none"
              >
                <option value="">Semua Role</option>
                <option v-for="role in roles" :key="role.id" :value="role.id">
                  {{ capitalizeRole(role.name) }}
                </option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-black mb-2">Filter Status</label>
            <div class="relative">
              <select
                v-model="searchForm.status_filter"
                class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-1 focus:ring-black focus:border-black transition-all bg-white cursor-pointer appearance-none"
              >
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Stats/Info -->
          <div class="flex items-end sm:col-span-2 lg:col-span-1">
            <div class="w-full p-3 bg-gray-50 rounded-lg border border-gray-200">
              <div class="text-xs text-gray-500 mb-1">Total User</div>
              <div class="text-lg font-semibold text-black">{{ users.total || 0 }}</div>
            </div>
          </div>
        </div>

        <!-- Clear Filters -->
        <div class="mt-4" v-if="hasActiveFilters">
          <button
            @click="clearFilters"
            class="text-sm text-gray-500 hover:text-gray-700 underline cursor-pointer"
          >
            Clear all filters
          </button>
        </div>
      </div>

      <!-- Users Table -->
      <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  User
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Role
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dibuat
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0">
                      <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700">
                          {{ getInitials(user.name) }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-black">{{ user.name }}</div>
                      <div class="text-sm text-gray-500">{{ user.email }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getRoleBadgeClass(user.role?.name)">
                    {{ capitalizeRole(user.role?.name) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="user.is_active ? 'bg-gray-100 text-gray-800' : 'bg-gray-100 text-gray-500'">
                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(user.created_at) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-1">
                    <button
                      @click="viewUser(user)"
                      class="p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-md transition-colors cursor-pointer"
                      title="Lihat Detail"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <button
                      @click="editUser(user)"
                      class="p-2 text-gray-600 hover:text-black hover:bg-gray-100 rounded-md transition-colors cursor-pointer"
                      title="Edit User"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                    <button
                      @click="confirmDelete(user)"
                      class="p-2 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors cursor-pointer"
                      title="Hapus User"
                      :disabled="user.id === $page.props.auth.user.id"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="users.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <Link
                v-if="users.prev_page_url"
                :href="users.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Previous
              </Link>
              <Link
                v-if="users.next_page_url"
                :href="users.next_page_url"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Next
              </Link>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <template v-for="link in users.links" :key="link.label">
                    <Link
                      v-if="link.url"
                      :href="link.url"
                      class="relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors"
                      :class="link.active 
                        ? 'bg-black text-white border-black' 
                        : 'bg-white text-gray-500 border-gray-300 hover:bg-gray-50'"
                      v-html="link.label"
                    />
                    <span
                      v-else
                      class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-300"
                      v-html="link.label"
                    />
                  </template>
                </nav>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada user</h3>
          <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan user baru.</p>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="relative border-b border-gray-100 p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-black">Tambah User Baru</h3>
                <p class="text-sm text-gray-500">Buat akun pengguna baru untuk sistem</p>
              </div>
            </div>
            <button @click="closeCreateModal" class="p-2 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Form Content -->
        <div class="p-6">
          <form @submit.prevent="createUser" class="space-y-6">
            <!-- User Information Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Personal
              </h4>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="text"
                      v-model="createForm.name"
                      placeholder="Masukkan nama lengkap"
                      class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                      required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Email Address <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="email"
                      v-model="createForm.email"
                      placeholder="contoh@email.com"
                      class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                      required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Security Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Keamanan Akun
              </h4>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="password"
                      v-model="createForm.password"
                      @input="debouncedPasswordValidation(createForm.password)"
                      placeholder="Minimal 8 karakter, huruf besar, kecil, dan angka"
                      :class="[
                        'w-full pl-10 pr-10 py-3 border rounded-xl bg-gray-50 focus:outline-none focus:bg-white transition-all',
                        validationErrors.password 
                          ? 'border-red-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-red-50' 
                          : isPasswordValid 
                            ? 'border-green-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-green-50'
                            : 'border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500'
                      ]"
                      required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5" :class="validationErrors.password ? 'text-red-400' : isPasswordValid ? 'text-green-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg v-if="validationErrors.password" class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      <svg v-else-if="isPasswordValid" class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                  <div v-if="validationErrors.password" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ validationErrors.password }}
                  </div>
                  <div v-else-if="isPasswordValid" class="mt-2 flex items-center text-sm text-green-600">
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Password memenuhi syarat
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Konfirmasi Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="password"
                      v-model="createForm.password_confirmation"
                      @input="debouncedConfirmPasswordValidation(createForm.password_confirmation)"
                      placeholder="Ulangi password yang sama"
                      :class="[
                        'w-full pl-10 pr-10 py-3 border rounded-xl bg-gray-50 focus:outline-none focus:bg-white transition-all',
                        validationErrors.password_confirmation 
                          ? 'border-red-300 focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-red-50' 
                          : isPasswordMatching 
                            ? 'border-green-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-green-50'
                            : 'border-gray-200 focus:ring-2 focus:ring-green-500 focus:border-green-500'
                      ]"
                      required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5" :class="validationErrors.password_confirmation ? 'text-red-400' : isPasswordMatching ? 'text-green-400' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg v-if="validationErrors.password_confirmation" class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      <svg v-else-if="isPasswordMatching" class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                  <div v-if="validationErrors.password_confirmation" class="mt-2 flex items-center text-sm text-red-600">
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ validationErrors.password_confirmation }}
                  </div>
                  <div v-else-if="isPasswordMatching" class="mt-2 flex items-center text-sm text-green-600">
                    <svg class="w-4 h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Password cocok
                  </div>
                </div>
              </div>
            </div>

            <!-- Role & Settings Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Role & Pengaturan
              </h4>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Role Pengguna <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <select
                      v-model="createForm.role_id"
                      class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-black focus:border-black focus:bg-white transition-all appearance-none cursor-pointer"
                      required
                    >
                      <option value="">Pilih Role</option>
                      <option v-for="role in roles" :key="role.id" :value="role.id">
                        {{ capitalizeRole(role.name) }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center">
                  <div class="bg-white border border-gray-200 rounded-xl p-4 w-full">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <input
                          type="checkbox"
                          v-model="createForm.is_active"
                          id="create_is_active"
                          class="h-5 w-5 text-black border-gray-300 rounded focus:ring-black focus:ring-2"
                        />
                        <label for="create_is_active" class="ml-3 block text-sm font-medium text-black">
                          Status Aktif
                        </label>
                      </div>
                      <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" :class="createForm.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                        <span class="text-xs font-medium" :class="createForm.is_active ? 'text-green-600' : 'text-gray-500'">
                          {{ createForm.is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                      </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">User dapat login dan mengakses sistem</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
              <button
                type="button"
                @click="closeCreateModal"
                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium border border-gray-200"
              >
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal
              </button>
              <button
                type="submit"
                :class="[
                  'px-6 py-3 rounded-xl transition-colors text-sm font-medium shadow-lg border-2',
                  isCreateFormValid 
                    ? '!bg-black !text-white hover:!bg-gray-800 border-black' 
                    : 'bg-gray-300 text-gray-500 cursor-not-allowed border-gray-300'
                ]"
                :disabled="!isCreateFormValid"
              >
                <svg v-if="!createLoading" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <svg v-else class="w-4 h-4 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ createLoading ? 'Menyimpan...' : 'Buat User' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- View User Modal -->
    <div v-if="showViewModal" class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="relative border-b border-gray-100 p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-black">Detail User</h3>
                <p class="text-sm text-gray-500">Informasi lengkap pengguna sistem</p>
              </div>
            </div>
            <button @click="showViewModal = false" class="p-2 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="userDetailsLoading" class="p-8 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-black mx-auto mb-4"></div>
          <p class="text-gray-500">Memuat detail user...</p>
        </div>
        
        <!-- User Details -->
        <div v-else-if="userDetails" class="p-6">
          <!-- User Profile Section -->
          <div class="bg-gradient-to-br from-gray-50 to-white rounded-2xl p-6 mb-6 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-6">
              <!-- Avatar -->
              <div class="flex-shrink-0">
                <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center shadow-lg">
                  <span class="text-2xl font-bold text-gray-700">
                    {{ getInitials(userDetails.user.name) }}
                  </span>
                </div>
              </div>
              
              <!-- User Info -->
              <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <h4 class="text-2xl font-bold text-black mb-1">{{ userDetails.user.name }}</h4>
                    <p class="text-gray-600 mb-3">{{ userDetails.user.email }}</p>
                    <div class="flex flex-wrap gap-2">
                      <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-medium"
                            :class="getRoleBadgeClass(userDetails.user.role?.name)">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        {{ capitalizeRole(userDetails.user.role?.name) }}
                      </span>
                      <span class="inline-flex items-center px-3 py-1 rounded-xl text-sm font-medium"
                            :class="userDetails.user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                        <div class="w-2 h-2 rounded-full mr-1.5"
                             :class="userDetails.user.is_active ? 'bg-green-500' : 'bg-red-500'"></div>
                        {{ userDetails.user.is_active ? 'Aktif' : 'Nonaktif' }}
                      </span>
                    </div>
                  </div>
                  
                  <!-- Quick Actions -->
                  <div class="flex space-x-2 mt-4 sm:mt-0">
                    <button @click="editUser(userDetails.user)" class="p-2 bg-black text-white rounded-xl hover:bg-gray-800 transition-colors cursor-pointer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Statistics Grid -->
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-2xl font-bold text-black">{{ userDetails.stats.total_orders }}</p>
                  <p class="text-sm text-gray-600">Total Pesanan</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-2xl font-bold text-green-600">{{ userDetails.stats.completed_orders }}</p>
                  <p class="text-sm text-gray-600">Selesai</p>
                </div>
                <div class="p-2 bg-green-100 rounded-lg">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-2xl font-bold text-black">{{ formatCurrency(userDetails.stats.total_spent) }}</p>
                  <p class="text-sm text-gray-600">Total Belanja</p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg">
                  <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                  </svg>
                </div>
              </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-2xl font-bold text-purple-600">{{ userDetails.stats.cart_items }}</p>
                  <p class="text-sm text-gray-600">Item Keranjang</p>
                </div>
                <div class="p-2 bg-purple-100 rounded-lg">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5H20" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Detailed Information -->
          <div class="grid md:grid-cols-2 gap-6">
            <!-- Account Information -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
              <h5 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Akun
              </h5>
              <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">ID User</span>
                  <span class="font-medium text-black">#{{ userDetails.user.id }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Terdaftar</span>
                  <span class="font-medium text-black">{{ formatDate(userDetails.user.created_at) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Umur Akun</span>
                  <span class="font-medium text-black">{{ userDetails.stats.account_age_formatted }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Email Verified</span>
                  <span v-if="userDetails.user.email_verified_at" class="inline-flex items-center text-green-600 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Ya
                  </span>
                  <span v-else class="inline-flex items-center text-red-600 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Belum
                  </span>
                </div>
                <div class="flex justify-between items-center py-2">
                  <span class="text-gray-600">Aktivitas Terakhir</span>
                  <span class="font-medium text-black">{{ formatDate(userDetails.stats.last_activity) }}</span>
                </div>
              </div>
            </div>

            <!-- Order Statistics -->
            <div class="bg-white border border-gray-200 rounded-xl p-6">
              <h5 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Statistik Pesanan
              </h5>
              <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Total Pesanan</span>
                  <span class="font-bold text-blue-600">{{ userDetails.stats.total_orders }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Selesai</span>
                  <span class="font-bold text-green-600">{{ userDetails.stats.completed_orders }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Pending</span>
                  <span class="font-bold text-yellow-600">{{ userDetails.stats.pending_orders }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                  <span class="text-gray-600">Dibatalkan</span>
                  <span class="font-bold text-red-600">{{ userDetails.stats.cancelled_orders }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                  <span class="text-gray-600">Success Rate</span>
                  <span class="font-bold text-black">
                    {{ userDetails.stats.total_orders > 0 ? Math.round((userDetails.stats.completed_orders / userDetails.stats.total_orders) * 100) : 0 }}%
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="relative border-b border-gray-100 p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center shadow-lg">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-black">Edit User</h3>
                <p class="text-sm text-gray-500">Perbarui informasi pengguna sistem</p>
              </div>
            </div>
            <button @click="closeEditModal" class="p-2 hover:bg-gray-100 rounded-xl transition-colors cursor-pointer">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Form Content -->
        <div class="p-6">
          <form @submit.prevent="updateUser" class="space-y-6">
            <!-- User Information Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Informasi Personal
              </h4>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="text"
                      v-model="editForm.name"
                      placeholder="Masukkan nama lengkap"
                      class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                      required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Email Address <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="email"
                      v-model="editForm.email"
                      placeholder="contoh@email.com"
                      class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition-all"
                      required
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Security Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Update Password
                <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Opsional</span>
              </h4>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Password Baru
                  </label>
                  <div class="relative">
                    <input
                      type="password"
                      v-model="editForm.password"
                      placeholder="Kosongkan jika tidak ingin mengubah"
                      class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-all"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                      </svg>
                    </div>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">Minimal 8 karakter jika ingin mengubah</p>
                </div>
                
                <div v-if="editForm.password">
                  <label class="block text-sm font-semibold text-black mb-2">
                    Konfirmasi Password Baru <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input
                      type="password"
                      v-model="editForm.password_confirmation"
                      placeholder="Ulangi password baru"
                      class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white transition-all"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div v-else class="flex items-center justify-center">
                  <div class="text-center text-gray-500">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm">Password tidak akan berubah</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Role & Settings Section -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-6 border border-gray-100">
              <h4 class="text-lg font-semibold text-black mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                Role & Pengaturan
              </h4>
              
              <div class="grid md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-black mb-2">
                    Role Pengguna <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <select
                      v-model="editForm.role_id"
                      class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-black focus:border-black focus:bg-white transition-all appearance-none cursor-pointer"
                      required
                    >
                      <option value="">Pilih Role</option>
                      <option v-for="role in roles" :key="role.id" :value="role.id">
                        {{ capitalizeRole(role.name) }}
                      </option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                      </svg>
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center">
                  <div class="bg-white border border-gray-200 rounded-xl p-4 w-full">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center">
                        <input
                          type="checkbox"
                          v-model="editForm.is_active"
                          id="edit_is_active"
                          class="h-5 w-5 text-black border-gray-300 rounded focus:ring-black focus:ring-2"
                        />
                        <label for="edit_is_active" class="ml-3 block text-sm font-medium text-black">
                          Status Aktif
                        </label>
                      </div>
                      <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" :class="editForm.is_active ? 'bg-green-500' : 'bg-gray-400'"></div>
                        <span class="text-xs font-medium" :class="editForm.is_active ? 'text-green-600' : 'text-gray-500'">
                          {{ editForm.is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                      </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">User dapat login dan mengakses sistem</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
              <button
                type="button"
                @click="closeEditModal"
                class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-sm font-medium border border-gray-200"
              >
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal
              </button>
              <button
                type="submit"
                class="px-6 py-3 !bg-black !text-white rounded-xl hover:!bg-gray-800 transition-colors text-sm font-medium shadow-lg border-2 border-black"
                :disabled="editLoading"
              >
                <svg v-if="!editLoading" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <svg v-else class="w-4 h-4 mr-2 inline animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ editLoading ? 'Menyimpan...' : 'Update User' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black/20 backdrop-blur-md flex items-center justify-center z-[9999] p-4">
      <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-200">
        <!-- Modal Header -->
        <div class="relative border-b border-gray-100 p-6">
          <div class="flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
              <svg class="h-6 w-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </div>
            <div>
              <h3 class="text-xl font-bold text-black">Konfirmasi Hapus</h3>
              <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
            </div>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
          <!-- User Info -->
          <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl p-4 mb-6 border border-gray-100">
            <div class="flex items-center space-x-3">
              <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <span class="text-sm font-medium text-gray-700">
                  {{ getInitials(userToDelete?.name || '') }}
                </span>
              </div>
              <div>
                <h4 class="font-semibold text-black">{{ userToDelete?.name }}</h4>
                <p class="text-sm text-gray-600">{{ userToDelete?.email }}</p>
              </div>
            </div>
          </div>

          <!-- Confirmation Message -->
          <div class="text-center mb-6">
            <p class="text-gray-700">
              Apakah Anda yakin ingin menghapus user ini?
            </p>
            <p class="text-sm text-gray-500 mt-2">
              Data user akan dihapus secara permanen dari sistem.
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 p-6 border-t border-gray-100">
          <button 
            @click="showDeleteModal = false"
            class="px-6 py-2.5 !text-gray-700 !bg-gray-100 rounded-lg hover:!bg-gray-200 transition-colors text-sm font-medium border border-gray-300"
          >
            Batal
          </button>
          <button 
            @click="deleteUser"
            class="px-6 py-2.5 !bg-red-600 !text-white rounded-lg hover:!bg-red-700 transition-colors text-sm font-medium border border-red-600"
          >
            Hapus
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({
  users: Object,
  roles: Array,
  filters: Object
})

const searchForm = ref({
  search: props.filters.search || '',
  role_filter: props.filters.role_filter !== undefined ? props.filters.role_filter : '3', // Default to user role (ID: 3)
  status_filter: props.filters.status_filter !== undefined ? props.filters.status_filter : '1' // Default to active users
})

// Modal states
const showDeleteModal = ref(false)
const showCreateModal = ref(false)
const showViewModal = ref(false)
const showEditModal = ref(false)

// Form states
const createForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: '',
  is_active: true
})

// Validation states
const validationErrors = ref({
  password: '',
  password_confirmation: ''
})

const isPasswordValid = ref(false)
const isPasswordMatching = ref(false)

const editForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role_id: '',
  is_active: true
})

// Loading states
const createLoading = ref(false)
const editLoading = ref(false)

// Selected user for operations
const userToDelete = ref(null)
const selectedUser = ref(null)
const userDetails = ref(null)
const userDetailsLoading = ref(false)

const hasActiveFilters = computed(() => {
  return searchForm.value.search || 
         (searchForm.value.role_filter !== '3' && searchForm.value.role_filter !== '') || 
         (searchForm.value.status_filter !== '1' && searchForm.value.status_filter !== '')
})

const isCreateFormValid = computed(() => {
  return createForm.value.name &&
         createForm.value.email &&
         createForm.value.role_id &&
         isPasswordValid.value &&
         isPasswordMatching.value &&
         !createLoading.value
})

// Debounce function
let searchTimeout = null
const performSearch = () => {
  router.get('/admin/users', searchForm.value, {
    preserveState: true,
    replace: true
  })
}

// Watch for search input with debounce
watch(() => searchForm.value.search, (newValue) => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
  
  searchTimeout = setTimeout(() => {
    performSearch()
  }, 500) // 500ms debounce
})

// Watch for filter changes (immediate)
watch(() => searchForm.value.role_filter, () => {
  performSearch()
})

watch(() => searchForm.value.status_filter, () => {
  performSearch()
})

const clearFilters = () => {
  searchForm.value = {
    search: '',
    role_filter: '3', // Reset to default user role
    status_filter: '1' // Reset to default active status
  }
  // performSearch will be triggered by watchers
}

const getInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2)
}

const getRoleBadgeClass = (roleName) => {
  const classes = {
    'admin': 'bg-black text-white',
    'staff': 'bg-gray-800 text-white',
    'user': 'bg-gray-100 text-gray-800'
  }
  return classes[roleName?.toLowerCase()] || 'bg-gray-100 text-gray-800'
}

const capitalizeRole = (roleName) => {
  if (!roleName) return 'N/A'
  return roleName.charAt(0).toUpperCase() + roleName.slice(1).toLowerCase()
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount || 0)
}

// Validation functions
let passwordValidationTimeout = null
let confirmPasswordValidationTimeout = null

const validatePassword = (password) => {
  const errors = []
  
  if (!password) {
    errors.push('Password wajib diisi')
  } else {
    if (password.length < 8) {
      errors.push('Minimal 8 karakter')
    }
    if (!/[A-Z]/.test(password)) {
      errors.push('Harus ada huruf besar')
    }
    if (!/[a-z]/.test(password)) {
      errors.push('Harus ada huruf kecil')
    }
    if (!/[0-9]/.test(password)) {
      errors.push('Harus ada angka')
    }
  }
  
  return errors
}

const validatePasswordConfirmation = (password, confirmation) => {
  if (!confirmation) {
    return 'Konfirmasi password wajib diisi'
  }
  
  if (password !== confirmation) {
    return 'Password tidak cocok'
  }
  
  return ''
}

const debouncedPasswordValidation = (password) => {
  if (passwordValidationTimeout) {
    clearTimeout(passwordValidationTimeout)
  }
  
  passwordValidationTimeout = setTimeout(() => {
    const errors = validatePassword(password)
    validationErrors.value.password = errors.length > 0 ? errors[0] : ''
    isPasswordValid.value = errors.length === 0 && password.length > 0
    
    // Also revalidate confirmation if it exists
    if (createForm.value.password_confirmation) {
      debouncedConfirmPasswordValidation(createForm.value.password_confirmation)
    }
  }, 500)
}

const debouncedConfirmPasswordValidation = (confirmation) => {
  if (confirmPasswordValidationTimeout) {
    clearTimeout(confirmPasswordValidationTimeout)
  }
  
  confirmPasswordValidationTimeout = setTimeout(() => {
    const error = validatePasswordConfirmation(createForm.value.password, confirmation)
    validationErrors.value.password_confirmation = error
    isPasswordMatching.value = !error && confirmation.length > 0
  }, 300)
}

// Modal functions
const closeCreateModal = () => {
  showCreateModal.value = false
  createForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    is_active: true
  }
  
  // Reset validation states
  validationErrors.value = {
    password: '',
    password_confirmation: ''
  }
  isPasswordValid.value = false
  isPasswordMatching.value = false
  
  // Clear timeouts
  if (passwordValidationTimeout) {
    clearTimeout(passwordValidationTimeout)
  }
  if (confirmPasswordValidationTimeout) {
    clearTimeout(confirmPasswordValidationTimeout)
  }
}

const closeEditModal = () => {
  showEditModal.value = false
  editForm.value = {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
    is_active: true
  }
  selectedUser.value = null
}

const viewUser = async (user) => {
  selectedUser.value = user
  showViewModal.value = true
  userDetailsLoading.value = true
  userDetails.value = null
  
  try {
    console.log('Fetching user details for user ID:', user.id)
    const response = await fetch(`/admin/users/${user.id}/details`, {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    
    console.log('Response status:', response.status)
    
    if (!response.ok) {
      const errorText = await response.text()
      console.error('Response error:', errorText)
      throw new Error(`HTTP ${response.status}: ${errorText}`)
    }
    
    const data = await response.json()
    console.log('User details loaded:', data)
    userDetails.value = data
  } catch (error) {
    console.error('Error loading user details:', error)
    // Show error message to user
    alert('Gagal memuat detail user. Silakan coba lagi.')
  } finally {
    userDetailsLoading.value = false
  }
}

const editUser = (user) => {
  // Close view modal if open
  showViewModal.value = false
  
  selectedUser.value = user
  editForm.value = {
    name: user.name,
    email: user.email,
    password: '',
    password_confirmation: '',
    role_id: user.role_id,
    is_active: user.is_active
  }
  showEditModal.value = true
}

const confirmDelete = (user) => {
  userToDelete.value = user
  showDeleteModal.value = true
}

// CRUD operations
const createUser = () => {
  createLoading.value = true
  
  router.post('/admin/users', createForm.value, {
    onSuccess: () => {
      closeCreateModal()
      createLoading.value = false
    },
    onError: () => {
      createLoading.value = false
    }
  })
}

const updateUser = () => {
  if (!selectedUser.value) return
  
  editLoading.value = true
  
  router.put(`/admin/users/${selectedUser.value.id}`, editForm.value, {
    onSuccess: () => {
      closeEditModal()
      editLoading.value = false
    },
    onError: () => {
      editLoading.value = false
    }
  })
}

const deleteUser = () => {
  if (userToDelete.value) {
    router.delete(`/admin/users/${userToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false
        userToDelete.value = null
      }
    })
  }
}
</script>
